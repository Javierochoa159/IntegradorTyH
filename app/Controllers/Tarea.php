<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\UsuarioModel;
use App\Models\SubTareaModel;
use App\Models\ComentarioModel;
use App\Models\TareaCompartidaModel;
use Error;

class Tarea extends BaseController{
    public function __construct(){ 
        helper ('form');
        session()->set("orden",1);
    }
    public function index($id){
        if(isset(session()->get("ids")[$id-1])){
            $datos=$this->todasLasSubTareas($id);
            $datos["urlTarea"]="tarea";
            return view("tareaView",$datos);
        }else return redirect()->to(base_url()."inicio")->with("mensaje",["error" => "", "mensaje" => "Tarea no encontrada"]);
    }

    public function newSubTarea(){
        try{
            if(!isset(session()->get("ids")[$this->request->getPost("idTarea")-1])){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado al momento de crear una SubTarea<br>Intentelo nuevamente en unos minutos"]);
            }else{
                helper("spanishErrors_helper");
                $post=$this->request->getPost(["descripcionSubTarea","comentarioSubTarea","prioridadSubTarea","colorSubTarea","responsableSubTarea","fechaVencimientoSubTarea","idTarea"]);
                $validacion = service('validation');
                $reglas=[
                    "descripcionSubTarea" =>"required|valid_alphanum_space_punct|max_length[255]|min_length[10]",
                    "fechaVencimientoSubTarea" =>"required|valid_date"
                ];
                if($post["responsableSubTarea"]!=null)$reglas["responsableSubTarea"]="min_length[4]|max_length[15]|valid_alphanum_dash";
                if($post["comentarioSubTarea"]!=null) $reglas["comentarioSubTarea"]="valid_alphanum_space_punct|max_length[50]|min_length[7]";
                $validacion->setRules($reglas,spanishErrorMessages($reglas));
                if (!$validacion->withRequest($this->request)->run())
                {
                    return redirect()->to(base_url()."tarea/".$post["idTarea"])->withInput();
                }
                $user=new UsuarioModel();
                $data=[];
                $sqlIn=[
                    "descripcionSubTarea"=>$post["descripcionSubTarea"],
                    "prioridadSubTarea"=>$post["prioridadSubTarea"],
                    "colorSubTarea"=>$post["colorSubTarea"],
                    "fechaVencimientoSubTarea"=>$post["fechaVencimientoSubTarea"],
                    "autorSubTarea"=> session()->get("usuario")["id"],
                    "idTarea"=>session()->get("ids")[$post["idTarea"]-1]
                ];
                if($post["responsableSubTarea"]!=null){
                    $mensaje=array();
                    $data=$user->select("idUsuario")->where('usuarioUsuario',$post["responsableSubTarea"])->find();
                    $user=null;
                    if(empty($data)){
                        $mensaje["responsableSubTarea"]="El usuario ingresado no existe.";
                    }
                    if(!empty($mensaje)){
                        return redirect()->back()->withInput()->with('mensaje',$mensaje);
                    }
                    $sqlIn["responsableSubTarea"]==$data[0];
                }else{
                    $sqlIn["responsableSubTarea"]=session()->get("usuario")["id"];
                }
                $subTarea=new SubTareaModel();
                $idSubTarea=$subTarea->insert($sqlIn,true);
                $subTarea=null;
                if($idSubTarea){
                    if($post["comentarioSubTarea"]!=null){
                        $comentario=new ComentarioModel();
                        $sqlIn=[
                            "idSubTarea"=>$idSubTarea,
                            "idUsuario"=>session()->get("usuario")["id"],
                            "comentario"=>$post["comentarioSubTarea"]
                        ];
                        if(!$comentario->insert($sqlIn)){
                            $comentario=null;
                            return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "Error al crear el comentarios de la subTarea"]);
                        }
                        $comentario=null;
                    }
                    return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["success"=> "", "mensaje" => "SubTarea creada!"]);
                }else{
                    return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "Error al crear la subTarea<br>Intente nuevamente en unos minutos"]);
                }
            }
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }

    private function todasLasSubTareas($id){
        try{
            $db = \Config\Database::connect();
            $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.descripcionTarea AS descripcion, tareas.prioridadTarea AS prioridad, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, tareas.autorTarea AS autor, "tarea" AS tarea_subtarea
                                        FROM tareas
                                        LEFT JOIN tareasCompartidas ON tareasCompartidas.idTarea=tareas.idTarea
                                        WHERE tareas.idTarea='.session()->get("ids")[$id-1].'
                                              AND (
                                                   (tareas.autorTarea = '.session()->get("usuario")["id"].' AND tareas.tareaArchivada = 0)
                                                  OR
                                                   (tareasCompartidas.estadoTareaCompartida="1" AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].')
                                                  )
                                        UNION
                                            SELECT subTareas.idSubTarea AS id, "" AS titulo, subTareas.descripcionSubTarea AS descripcion, subTareas.prioridadSubTarea AS prioridad, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, "" AS fechaRecordatorio, subTareas.colorSubTarea AS color, subTareas.autorSubTarea AS autor, "subtarea" AS tarea_subtarea
                                            FROM subTareas
                                            LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea=subTareas.idSubTarea
                                            WHERE subTareas.idTarea = '.session()->get("ids")[$id-1].'
                                                  AND (subTareas.autorSubTarea = '.session()->get("usuario")["id"].'
                                                       OR
                                                       (tareasCompartidas.estadoTareaCompartida="1" AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].')
                                                      )
                                        ';
            $sql.=$this->getOrden();
            $query   = $db->query($sql);
            $datos["tarea_subTareas"] = $query->getResultArray();
            $db->close();
            $aux=[];
            foreach ($datos["tarea_subTareas"] as $tareaOsubtarea) {
                if($tareaOsubtarea["tarea_subtarea"]=="tarea") continue;
                else{
                    $aux[]=$tareaOsubtarea["id"];
                }
            }
            session()->set("idsTarea",$aux);
            $datos["ids"] = $aux;
            $datos["idTarea"]=$id;
            return $datos;
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }
    public function todas($id,$orden=1){
        if(isset(session()->get("ids")[$id-1])){
            $this->setOrden($orden);
            return redirect()->to(base_url()."tarea/".$id);
        }
    }
    private function setOrden($orden){
        switch($orden){
            case 1: session()->set("orden",1); break;
            case 2: session()->set("orden",2); break;
            case 3: session()->set("orden",3); break;
        }
    }
    private function getOrden(){
        switch(session()->get("orden")){
            case 1: return "Order By id DESC";
            case 2: return "Order By fechaVencimiento";
            case 3: return "Order By prioridad";
        }
    }

    public function setEstadoTarea($id){
        try{
            if(isset(session()->get("ids")[$id-1])){
                $idTarea=session()->get("ids")[$id-1];
                $tarea=new TareaModel();
                $estado=$tarea->select("estadoTarea")->find($idTarea);
                if($estado!=null){
                    switch($estado["estadoTarea"]){
                        case 1: if(!$tarea->update($idTarea,["estadoTarea"=>"2"])){
                                    $tarea=null;
                                    return redirect()->to(base_url()."tarea/".$id)->with("error",["mensaje"=>"No se pudo modificar el estado de la Tarea<br>Intente nuevamente en unos minutos"]);
                                }
                                $tarea=null;
                                return redirect()->to(base_url()."tarea/".$id)->with("success",["mensaje"=>"Estado modificado con exito"]);
                        case 2: $db = \Config\Database::connect();
                                $sql='  SELECT COALESCE(SUM(CASE WHEN estadoSubTarea = "3" THEN 1 ELSE 0 END), 0) AS totalFinalizadas,
                                               COUNT(estadoSubTarea) AS totalSubTareas
                                               
                                        FROM subTareas
                                        WHERE idTarea='.$idTarea.'
                                      ';
                                $query   = $db->query($sql);
                                $datos = $query->getResultArray();
                                $db->close();
                                if(!isset($datos[0]["totalFinalizadas"])||!isset($datos[0]["totalSubTareas"])){
                                    return redirect()->to(base_url()."tarea/".$id)->with("mensajeTarea",["error"=>"","mensaje"=>"No se pudo modificar el estado de la Tarea<br>Intente nuevamente en unos minutos"]);
                                }elseif($datos[0]["totalFinalizadas"]!=$datos[0]["totalSubTareas"]){
                                    return redirect()->to(base_url()."tarea/".$id)->with("mensajeTarea",["error"=>"","mensaje"=>"No se pudo modificar el estado de la Tarea<br>Primero finalice todas las subTareas"]);
                                }elseif(!$tarea->update($idTarea,["estadoTarea"=>"3"])){
                                    return redirect()->to(base_url()."tarea/".$id)->with("mensajeTarea",["error"=>"","mensaje"=>"No se pudo modificar el estado de la Tarea<br>Intente nuevamente en unos minutos"]);
                                }
                                return redirect()->to(base_url()."tarea/".$id)->with("mensajeTarea",["success"=>"","mensaje"=>"Estado modificado con exito"]);
                    }
                }            
            }
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }

    public function archivarTarea($id){
        try{
            if(!isset(session()->get("ids")[$id-1])){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Tarea no encontrada."]);
            }
            $idTarea=session()->get("ids")[$id-1];
            $tarea=new TareaModel;
            if(!$tarea->update($idTarea,["tareaArchivada"=>true])){
                return redirect()->to("tarea/".$id)->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar archivar la tarea.<br>Intente nuevamente en unos minutos."]);
            }
            $arrayIds=session()->get("ids");
            array_splice($arrayIds,0,$id,array_slice($arrayIds,0,$id-1));
            session()->set("ids",$arrayIds);
            $auxArrayidsUser=session()->get("idsUsuario");
            $auxArrayidsUser[]=$idTarea;
            session()->set("idsUsuario",$auxArrayidsUser);
            return redirect()->to(base_url()."historial/".sizeof($auxArrayidsUser))->with("mensajeTarea",["success"=>"","mensaje"=>"Tarea archivada con exito"]);
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello."]);
        }
    }

    public function modTarea($id){
        try{
            if(isset(session()->get("ids")[$id-1])){
                helper("spanishErrors_helper");
                $post=$this->request->getPost(["tituloTarea","descripcionTarea","prioridadTarea","colorTarea","fechaRecordatorioTarea"]);
                $validacion = service('validation');
                $reglas=[
                    "tituloTarea" =>"required|valid_alphanum_space|max_length[30]|min_length[6]",
                    "descripcionTarea" =>"required|valid_alphanum_space_punct|max_length[255]|min_length[10]"
                ];
                if($post["fechaRecordatorioTarea"]!=null) $reglas["fechaRecordatorioTarea"]="valid_date";
                $validacion->setRules($reglas,spanishErrorMessages($reglas));
                if (!$validacion->withRequest($this->request)->run())
                {
                    return redirect()->to(base_url()."tarea/".$id)->withInput();
                }
                $sqlIn=[
                    "tituloTarea"=>$post["tituloTarea"],
                    "descripcionTarea"=>$post["descripcionTarea"],
                    "prioridadTarea"=>$post["prioridadTarea"],
                    "colorTarea"=>$post["colorTarea"]
                ];
                if($post["fechaRecordatorioTarea"]!=null) $sqlIn["fechaRecordatorioTarea"]=$post["fechaRecordatorioTarea"];
                $tarea=new TareaModel();
                if($tarea->update(session()->get("ids")[$id-1],$sqlIn)){
                    $tarea=null;
                    return redirect()->to(base_url().'tarea/'.$id)->with("mensaje",["success"=> "", "mensaje" => "Tarea modificada!"]);
                }else{
                    $tarea=null;
                    return redirect()->to(base_url().'tarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la tarea<br>Intente nuevamente en unos minutos"]);
                }
            }
            return redirect()->to(base_url().'tarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la tarea<br>Intente nuevamente en unos minutos"]);
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }

    public function shareTarea(){
        try{
            helper("spanishErrors_helper");
            $post=$this->request->getPost(["usuarioCompartirTarea","accesibilidadCompartirTarea","idTarea"]);
            $validacion = service('validation');
            $reglas["usuarioCompartirTarea"]="required|min_length[4]|max_length[15]|valid_alphanum_dash";
            $validacion->setRules($reglas,spanishErrorMessages($reglas));
            if (!$validacion->withRequest($this->request)->run())
            {
                return redirect()->to(base_url()."tarea/".$post["idTarea"])->withInput();
            }
            $user=new UsuarioModel();
            $data=[];        
            $mensaje=array();
            $data=$user->select("idUsuario, emailUsuario")->where('usuarioUsuario',$post["usuarioCompartirTarea"])->find();
            $user=null;
            if(empty($data)){
                $mensaje["usuarioCompartirTarea"]="El usuario ingresado no existe.";
            }elseif($data[0]["idUsuario"]==session()->get("usuario")["id"]){
                $mensaje["usuarioCompartirTarea"]="Ingrese un usuario distinto del suyo.";
            }
            if(!empty($mensaje)){
                return redirect()->back()->withInput()->with('mensaje',$mensaje);
            }
            $sqlIn=[
                "idUsuario" =>$data[0]["idUsuario"],
                "tipoTareaCompartida"=>$post["accesibilidadCompartirTarea"],
                "idTarea"=>session()->get("ids")[$post["idTarea"]-1]
            ];
            $tc=new TareaCompartidaModel();
            $res=$tc->select("tipoTareaCompartida, estadoTareaCompartida")->where("idTarea=".session()->get("ids")[$post["idTarea"]-1]." AND idUsuario=".$data[0]["idUsuario"])->find();
            if(!empty($res)){
                if($res[0]["tipoTareaCompartida"]!=$sqlIn["tipoTareaCompartida"] && $res[0]["estadoTareaCompartida"]==1){
                    if(!$tc->where("idTarea=".session()->get("ids")[$post["idTarea"]-1]." AND idUsuario=".$data[0]["idUsuario"])->update(null,["tipoTareaCompartida"=>$sqlIn['tipoTareaCompartida']]))
                    $tc=null;
                    return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "El usuario ya a aceptado la invitacion previamente<br>No se pudo modificar la accesibilidad. Intente nuevamente en unos minutos"]);
                }elseif($res[0]["estadoTareaCompartida"]==1){
                    $tc=null;
                    return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "El usuario ya a aceptado la invitación previamente"]);
                }elseif($res[0]["estadoTareaCompartida"]==0){
                    $tc=null;
                    return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "La invitación ya a sido enviada previamente<br>El usuario aun tiene que aceptarla"]);
                }
            }
            $idTC=$tc->insert($sqlIn,true);
            $tc=null;
            if($idTC){
                $email=\Config\Services::email();
                $email->setFrom("correoprueba.proyectos99@gmail.com","IntegradorTyH","rechazados@inbox.mailtrap.io");
                $email->setReplyTo("respuestas@inbox.mailtrap.io");
                $email->setTo($data[0]["emailUsuario"]);
                $email->setSubject("Invitacion a Tarea");
                $email->setMessage("Has recibido una invitacion a una tarea en tu cuenta<br><a href='".base_url()."/tarea/procesarshare/".$idTC."/1'>Aceptar</a><a href='".base_url()."/tarea/procesarshare/".$idTC."/2'>Rechazar</a>");
                if(!$email->send()){
                    return redirect()->to(base_url()."tarea/".$post["idTarea"])->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al enviar la invitacion<br>Intente nuevamente en unos minutos"]);
                }
                return redirect()->to(base_url()."tarea/".$post["idTarea"])->with("mensaje",["success"=>"","mensaje"=>"Invitacion a la tarea enviada exitosamente"]);
            }else{
                return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "Ocurrio un error al compartir la tarea<br>Intente nuevamente en unos minutos"]);
            }
            
        }
        catch(Error $e){
            return redirect()->to(base_url()."tarea/".$post["idTarea"])->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Intente nuevamente en unos minutos"]);
        }
    }

    public function subTarea($id){
        if(!isset(session()->get("idsTarea")[$id-1])) return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al ingresar a la subTarea"]);
        else return redirect()->to(base_url()."subtarea/".$id);
    }

    public function procesarShare($idTC, $estado){
        try{
            if($estado!=1 && $estado!=2){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }
            $tc=new TareaCompartidaModel();
            $res=$tc->select("estadoTareaCompartida")->find($idTC);
            if(empty($res)){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }elseif($res["estadoTareaCompartida"]!=0){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Esta invitacion ya a sido procesada"]);
            }
            if(!$tc->update($idTC,["estadoTareaCompartida"=>$estado])){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"La invitacion se proceso con exito"]);
        }catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
        }
    }
}