<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\UsuarioModel;
use App\Models\SubTareaModel;
use App\Models\ComentarioModel;
use App\Models\TareaCompartidaModel;
use Error;

class SubTarea extends BaseController{
    public function __construct(){ 
        helper ('form');
    }
    public function index($id){
        if(isset(session()->get("idsTarea")[$id-1])){
            $datos=$this->todosLosComentarios(session()->get("idsTarea")[$id-1]);
        }elseif(session()->get("subTareaShare")[$id]==session()->get("ids")[$id-1]){
            $datos=$this->todosLosComentarios(session()->get("subTareaShare")[$id]);
        }else{
            return redirect()->to(base_url()."inicio")->with("mensaje",["error" => "", "mensaje" => "SubTarea no encontrada"]);
        }
        if($datos==null){
            redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al inesperado. Estamos trabajando en ello"]);
        }
        $datos["idSubTarea"]=$id;
        return view("subTareaView",$datos);
    }
    private function todosLosComentarios($id){
        try{
            $db = \Config\Database::connect();
            $sql='SELECT subTareas.idSubTarea AS id, subTareas.descripcionSubTarea AS descripcion, subTareas.prioridadSubTarea AS prioridad, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, subTareas.colorSubTarea AS color, subTareas.autorSubTarea AS autor, "subtarea" AS subtarea_comentario
                                        FROM subTareas
                                        LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea=subTareas.idSubTarea
                                        WHERE subTareas.idSubTarea = '.$id.'
                                                AND (subTareas.autorSubTarea = '.session()->get("usuario")["id"].'
                                                     OR
                                                     (tareasCompartidas.estadoTareaCompartida="1" 
                                                      AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].')
                                                    )
                                        UNION
                                            SELECT comentarios.idComentario AS id, comentarios.comentario AS descripcion, "" AS prioridad, "" AS estado, "" AS fechaVencimiento, "" AS color, comentarios.idUsuario AS autor, "comentario" AS subtarea_comentario
                                            FROM comentarios
                                            LEFT JOIN subTareas ON subTareas.idSubTarea= comentarios.idSubTarea
                                            LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea= comentarios.idSubTarea
                                            WHERE   comentarios.estado = 1
                                                    AND comentarios.idSubTarea='.$id.'
                                                    AND (subTareas.autorSubTarea = '.session()->get("usuario")["id"].' 
                                                         OR
                                                         (tareasCompartidas.estadoTareaCompartida="1" 
                                                          AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].')
                                                        )
                                        ORDER BY id DESC
                                        ';
            $query   = $db->query($sql);
            $datos["subTarea_comentarios"] = $query->getResultArray();
            $db->close();
            return $datos;
        }
        catch(Error $e){
            return null;
        }
    }

    public function newComentario(){
        try{
            if(!isset(session()->get("idsTarea")[$this->request->getPost("idSubTarea")-1])){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado al momento de crear un Comentario<br>Intentelo nuevamente en unos minutos"]);
            }else{
                helper("spanishErrors_helper");
                $post=$this->request->getPost(["comentarioComentario","idSubTarea"]);
                $validacion = service('validation');
                $reglas=[
                    "comentarioComentario" =>"required|valid_alphanum_space_punct|max_length[50]|min_length[7]"
                ];
                $validacion->setRules($reglas,spanishErrorMessages($reglas));
                if (!$validacion->withRequest($this->request)->run())
                {
                    return redirect()->to(base_url()."subtarea/".$post["idSubTarea"])->withInput();
                }else{
                    $sqlIn=[
                        "idSubTarea"=>session()->get("idsTarea")[$post["idSubTarea"]-1],
                        "idUsuario"=> session()->get("usuario")["id"],
                        "comentario"=>$post["comentarioComentario"]
                    ];
                    $subTarea=new ComentarioModel();
                    if(!$subTarea->insert($sqlIn)){
                        return redirect()->to(base_url().'subtarea/'.$post["idSubTarea"])->with("mensaje",["error"=> "", "mensaje"=> "Error al crear el comentario"]);
                    }else{
                        return redirect()->to(base_url().'subtarea/'.$post["idSubTarea"])->with("mensaje",["success"=> "", "mensaje" => "Comentario creado!"]);
                    }
                }
            }
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }

    public function modSubTarea($id){
        try{
            if(isset(session()->get("idsTarea")[$id-1])){
                helper("spanishErrors_helper");
                $post=$this->request->getPost(["descripcionSubTarea","prioridadSubTarea","colorSubTarea"]);
                $validacion = service('validation');
                $reglas=[
                    "descripcionSubTarea" =>"required|valid_alphanum_space_punct|max_length[255]|min_length[10]"
                ];
                $validacion->setRules($reglas,spanishErrorMessages($reglas));
                if (!$validacion->withRequest($this->request)->run())
                {
                    return redirect()->to(base_url()."tarea/".$id)->withInput();
                }
                $sqlIn=[
                    "descripcionSubTarea"=>$post["descripcionSubTarea"],
                    "prioridadSubTarea"=>$post["prioridadSubTarea"],
                    "colorSubTarea"=>$post["colorSubTarea"]
                ];
                $subTarea=new SubTareaModel();
                if($subTarea->update(session()->get("idsTarea")[$id-1],$sqlIn)){
                    $subTarea=null;
                    return redirect()->to(base_url().'subtarea/'.$id)->with("mensaje",["success"=> "", "mensaje" => "SubTarea modificada!"]);
                }else{
                    $subTarea=null;
                    return redirect()->to(base_url().'subtarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la subTarea<br>Intente nuevamente en unos minutos"]);
                }
            }
            return redirect()->to(base_url().'subtarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la subTarea<br>Intente nuevamente en unos minutos"]);
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }

    public function setEstadoSubTarea($id){
        try{
            if(isset(session()->get("idsTarea")[$id-1])){
                $idSubTarea=session()->get("idsTarea")[$id-1];
            }elseif(isset(session()->get("ids")[$id-1])){
                if(session()->get("subTareaShare")[$id]==session()->get("ids")[$id-1]){
                    $idSubTarea=session()->get("subTareaShare")[$id];
                }
            }
            if(isset($idSubTarea)){
                $subtarea=new SubTareaModel();
                $estado=$subtarea->select("estadoSubTarea")->find($idSubTarea);
                if($estado!=null){
                    switch($estado["estadoSubTarea"]){
                        case 1: if(!$subtarea->update($idSubTarea,["estadoSubTarea"=>"2"])){
                                    $subtarea=null;
                                    return redirect()->to(base_url()."subtarea/".$id)->with("error",["mensaje"=>"No se pudo modificar el estado de la SubTarea<br>Intente nuevamente en unos minutos"]);
                                }
                                $subtarea=null;
                                return redirect()->to(base_url()."subtarea/".$id)->with("success",["mensaje"=>"Estado modificado con exito"]);
                        case 2: if(!$subtarea->update($idSubTarea,["estadoSubTarea"=>"3"])){
                                    $subtarea=null;
                                    return redirect()->to(base_url()."subtarea/".$id)->with("error",["mensaje"=>"No se pudo modificar el estado de la SubTarea<br>Intente nuevamente en unos minutos"]);
                                }
                                $idUsuarioSubTarea=$subtarea->select("autorSubTarea")->find($idSubTarea);
                                $subtarea=null;
                                $tareaComp=new TareaCompartidaModel();
                                $res=$tareaComp->where("idSubTarea=".$idSubTarea." AND estadoTareaCompartida='1'")->update(null,["estadoTareaCompartida"=>'3']);
                                while(!$res){
                                    $res=$tareaComp->where("idSubTarea=".$idSubTarea." AND estadoTareaCompartida='1'")->update(null,["estadoTareaCompartida"=>'3']);
                                }
                                $tareaComp=null;
                                if(isset($idUsuarioSubTarea)){
                                    if(!empty($idUsuarioSubTarea)){
                                        if($idUsuarioSubTarea["autorSubTarea"]==session()->get("usuario")["id"]){
                                            return redirect()->to(base_url()."subtarea/".$id)->with("success",["mensaje"=>"SubTarea Finalizada con exito"]);
                                        }
                                    }
                                }
                                return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"SubTarea Finalizada con exito"]);
                    }
                }
            }else{
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello."]);
            }
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }

    public function shareSubTarea(){
        try{
            helper("spanishErrors_helper");
            $post=$this->request->getPost(["usuarioCompartirSubTarea","accesibilidadCompartirSubTarea","idSubTarea"]);
            $validacion = service('validation');
            $reglas["usuarioCompartirSubTarea"]="required|min_length[4]|max_length[15]|valid_alphanum_dash";
            $validacion->setRules($reglas,spanishErrorMessages($reglas));
            if (!$validacion->withRequest($this->request)->run())
            {
                return redirect()->to(base_url()."subtarea/".$post["idSubTarea"])->withInput();
            }
            $user=new UsuarioModel();
            $data=[];        
            $mensaje=array();
            $data=$user->select("idUsuario, emailUsuario")->where('usuarioUsuario',$post["usuarioCompartirSubTarea"])->find();
            $user=null;
            if(empty($data)){
                $mensaje["usuarioCompartirSubTarea"]="El usuario ingresado no existe.";
            }elseif($data[0]["idUsuario"]==session()->get("usuario")["id"]){
                $mensaje["usuarioCompartirSubTarea"]="Ingrese un usuario distinto del suyo.";
            }
            if(!empty($mensaje)){
                return redirect()->back()->withInput()->with('mensaje',$mensaje);
            }
            $tarea=new TareaModel();
            $idTarea=$tarea->select("tareas.idTarea")->join("subTareas","subTareas.idTarea=tareas.idTarea")->where("subTareas.idSubTarea=".session()->get("idsTarea")[$post["idSubTarea"]-1])->find();
            if(empty($idTarea)){
                return redirect()->to("subTarea/".$post["idSubTarea"])->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar compartir la subTarea.<br>Intente nuevamente en unos minutos."]);
            }
            $sqlIn=[
                "idUsuario" =>$data[0]["idUsuario"],
                "idTarea" =>$idTarea[0]["idTarea"],
                "tipoTareaCompartida"=>$post["accesibilidadCompartirSubTarea"],
                "idSubTarea"=>session()->get("idsTarea")[$post["idSubTarea"]-1]
            ];
            $tc=new TareaCompartidaModel();
            $res=$tc->select("tipoTareaCompartida, estadoTareaCompartida")->where("idTarea=".$idTarea[0]["idTarea"]." AND idSubTarea=".session()->get("idsTarea")[$post["idSubTarea"]-1]." AND idUsuario=".$data[0]["idUsuario"])->find();
            if(!empty($res)){
                if($res[0]["tipoTareaCompartida"]!=$sqlIn["tipoTareaCompartida"] && $res[0]["estadoTareaCompartida"]==1){
                    if(!$tc->where("idSubTarea=".session()->get("idsTarea")[$post["idSubTarea"]-1]." AND idUsuario=".$data[0]["idUsuario"])->update(null,["tipoTareaCompartida"=>$sqlIn['tipoTareaCompartida']]))
                    $tc=null;
                    return redirect()->to(base_url().'subtarea/'.$post["idSubTarea"])->with("mensaje",["error"=> "", "mensaje"=> "El usuario ya a aceptado la invitacion previamente<br>No se pudo modificar la accesibilidad. Intente nuevamente en unos minutos"]);
                }elseif($res[0]["estadoTareaCompartida"]==1){
                    $tc=null;
                    return redirect()->to(base_url().'subtarea/'.$post["idSubTarea"])->with("mensaje",["error"=> "", "mensaje"=> "El usuario ya a aceptado la invitación previamente"]);
                }elseif($res[0]["estadoTareaCompartida"]==0){
                    $tc=null;
                    return redirect()->to(base_url().'subtarea/'.$post["idSubTarea"])->with("mensaje",["error"=> "", "mensaje"=> "La invitación ya a sido enviada previamente<br>El usuario aun tiene que aceptarla"]);
                }
            }
            $idTC=$tc->insert($sqlIn,true);
            $tc=null;
            if($idTC){
                $email=\Config\Services::email();
                $email->setFrom("correoprueba.proyectos99@gmail.com","IntegradorTyH","rechazados@inbox.mailtrap.io");
                $email->setReplyTo("respuestas@inbox.mailtrap.io");
                $email->setTo($data[0]["emailUsuario"]);
                $email->setSubject("Invitacion a SubTarea");
                $email->setMessage("Has recibido una invitacion a una subtarea en tu cuenta<br><a href='".base_url()."/subtarea/procesarshare/".$idTC."/1'>Aceptar</a><a href='".base_url()."/subtarea/procesarshare/".$idTC."/2'>Rechazar</a>");
                if(!$email->send()){
                    return redirect()->to(base_url()."subtarea/".$post["idSubTarea"])->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al enviar la invitacion<br>Intente nuevamente en unos minutos"]);
                }
                return redirect()->to(base_url()."subtarea/".$post["idSubTarea"])->with("mensaje",["success"=>"","mensaje"=>"Invitacion a la subtarea enviada exitosamente"]);
            }else{
                return redirect()->to(base_url().'subtarea/'.$post["idSubTarea"])->with("mensaje",["error"=> "", "mensaje"=> "Ocurrio un error al compartir la subtarea<br>Intente nuevamente en unos minutos"]);
            }
            
        }
        catch(Error $e){
            return redirect()->to(base_url()."subtarea/".$post["idSubTarea"])->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Intente nuevamente en unos minutos"]);
        }
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
            return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"La invitacion se proceso con exito"]);
        }catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
        }
    }
    public function procesarResponsable($idTC, $estado, $estadoTC=0){
        try{
            if(($estado!=1 && $estado!=2) || ($estadoTC!=0 && $estadoTC!=1 && $estadoTC!=2)){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }
            $tc=new TareaCompartidaModel();
            $res=$tc->select("estadoTareaCompartida")->find($idTC);
            if(empty($res)){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }elseif($res["estadoTareaCompartida"]!=0){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Esta invitacion ya a sido procesada"]);
            }
            if($estado==1){
                $idSubTarea=$tc->select("idSubTarea, idUsuario")->find($idTC);
                if(!isset($idSubTarea)){
                    return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
                }
                if(empty($idSubTarea)){
                    return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
                }
                switch ($estadoTC){
                case 0: if(!$tc->update($idTC,["estadoTareaCompartida"=>$estado])){
                            $tc=null;
                            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
                        }
                        $tc=null;
                        $subTarea=new SubTareaModel();
                        $res=$subTarea->update($idSubTarea[0]["idSubTarea"],["responsableSubTarea"=>$idSubTarea[0]["idUsuario"]]);
                        while(!$res){
                            $res=$subTarea->update($idSubTarea[0]["idSubTarea"],["responsableSubTarea"=>$idSubTarea[0]["idUsuario"]]);
                        }
                        $subTarea=null;
                        return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"La invitacion se proceso con exito"]);
                
                case 1: if(!$tc->update($idTC,["tipoTareaCompartida"=>"3"])){
                            $tc=null;
                            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
                        }
                        $tc=null;
                        $subTarea=new SubTareaModel();
                        $res=$subTarea->update($idSubTarea[0]["idSubTarea"],["responsableSubTarea"=>$idSubTarea[0]["idUsuario"]]);
                        while(!$res){
                            $res=$subTarea->update($idSubTarea[0]["idSubTarea"],["responsableSubTarea"=>$idSubTarea[0]["idUsuario"]]);
                        }
                        $subTarea=null;
                        return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"La invitacion se proceso con exito"]);
                }
            }else{
                if(!$tc->update($idTC,["estadoTareaCompartida"=>$estado])){
                    $tc=null;
                    return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
                }
                $tc=null;
                return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"La invitacion se proceso con exito"]);
            }
        }catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
        }
    }
}