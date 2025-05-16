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
    }
    public function index($id){
        if(isset(session()->get("ids")[$id-1])){
            $datos=$this->todasLasSubTareas($id);
            if(isset($datos)){
                $datos["urlTarea"]="tarea";
                return view("tareaView",$datos);
            }else{
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello."]);
            }
        }else return redirect()->to(base_url()."inicio")->with("mensaje",["error" => "", "mensaje" => "Tarea no encontrada"]);
    }

    public function newSubTarea(){
        try{
            if(!isset(session()->get("ids")[$this->request->getPost("idTarea")-1])){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado al momento de crear una SubTarea<br>Intentelo nuevamente en unos minutos"]);
            }else{
                $tarea=new TareaModel();
                $idAutorT = $tarea->getAutorTarea(session()->get("ids")[$this->request->getPost("idTarea")-1]);
                $tarea=null;
                if(isset($idAutorT)){
                    if(!empty($idAutorT)){
                        if($idAutorT["autorTarea"]==session()->get("usuario")["id"]){
                            helper("spanishErrors_helper");
                            $post=$this->request->getPost(["descripcionSubTarea","comentarioSubTarea","prioridadSubTarea","colorSubTarea","responsableSubTarea","fechaVencimientoSubTarea","idTarea"]);
                            $validacion = service('validation');
                            $reglas=[
                                "descripcionSubTarea" =>"required|valid_alphanum_space_punct|max_length[255]|min_length[10]",
                            ];
                            if($post["fechaVencimientoSubTarea"]!=null)$reglas["fechaVencimientoSubTarea"]="valid_date";
                            if($post["responsableSubTarea"]!=null)$reglas["responsableSubTarea"]="min_length[4]|max_length[15]|valid_alphanum_dash";
                            if($post["comentarioSubTarea"]!=null) $reglas["comentarioSubTarea"]="valid_alphanum_space_punct|max_length[50]|min_length[7]";
                            $validacion->setRules($reglas,spanishErrorMessages($reglas));
                            if (!$validacion->withRequest($this->request)->run())
                            {
                                return redirect()->to(base_url()."tarea/".$post["idTarea"])->withInput();
                            }
                            $data=[];
                            $user=new UsuarioModel();
                            if($post["responsableSubTarea"]!=null){
                                $mensaje=array();
                                $data=$user->getIdEmailUser($post["responsableSubTarea"]);
                                $user=null;
                                if(empty($data)){
                                    $mensaje["responsableSubTarea"]="El usuario ingresado no existe.";
                                }
                                if(!empty($mensaje)){
                                    return redirect()->back()->withInput()->with('mensaje',$mensaje);
                                }
                            }
                            $subTarea=new SubTareaModel();
                            $idSubTarea=$subTarea->insertNewSubTarea($post["descripcionSubTarea"],
                                                                       $post["prioridadSubTarea"],
                                                                           $post["colorSubTarea"],
                                                                $post["fechaVencimientoSubTarea"],
                                                                                 session()->get("ids")[$post["idTarea"]-1]);
                            $subTarea=null;
                            if($idSubTarea){
                                if(isset($data[0]["emailUsuario"])){
                                    if($data[0]["idUsuario"]!=session()->get("usuario")["id"]){
                                        switch($this->cargarResponsable($data[0]["idUsuario"],$data[0]["emailUsuario"],$post["idTarea"],$idSubTarea)){
                                            case 0: $mensajeResponsable="Ocurrio un error al designar al responsable.";break;
                                            case 1: $mensajeResponsable="El usuario a sido notificado de la responsabilidad designada.";break;
                                            case 2: $mensajeResponsable="El usuario aun tiene que aceptar la invitacion.";
                                        }
                                    }
                                }
                                if($post["comentarioSubTarea"]!=null){
                                    $comentario=new ComentarioModel();
                                    if(!$comentario->insertNewComentario($idSubTarea,$post["comentarioSubTarea"])){
                                        $comentario=null;
                                        $mensajeComentario="Error al crear el comentarios de la subTarea";
                                    }
                                    $comentario=null;
                                }
                                $mensajeSuccess="SubTarea creada!";
                                if(isset($mensajeResponsable)){
                                    $mensajeSuccess.="<br>".$mensajeResponsable;
                                }
                                if(isset($mensajeComentario)){
                                    $mensaje.="<br>".$mensajeComentario;
                                }
                                return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["success"=> "", "mensaje" => $mensajeSuccess]);
                            }else{
                                return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "Error al crear la subTarea<br>Intente nuevamente en unos minutos"]);
                            }
                        }
                    }
                }
                return redirect()->to(base_url()."tarea/".$this->request->getPost("idTarea"))->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar crear la SubTarea.<br>Intente nuevamente en unos minutos."]);
            }
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }
    public function cargarResponsable($idUser,$emailUser,$idTarea,$idSubTarea){
        try{
            $tc=new TareaCompartidaModel();
            $res=$tc->getTCSubTarea(session()->get("ids")[$idTarea-1],$idUser,$idSubTarea);
            if(!isset($res)){
                $tc=null;
                return 0;
            }elseif(!empty($res)){
                $tc=null;
                if($res[0]["estadoTareaCompartida"]==0){
                    return 2;
                }elseif($res[0]["estadoTareaCompartida"]==1 && $res[0]["tipoTareaCompartida"]!="3"){
                    $email=\Config\Services::email();
                    $email->setFrom("correoprueba.proyectos99@gmail.com","IntegradorTyH","rechazados@inbox.mailtrap.io");
                    $email->setReplyTo("respuestas@inbox.mailtrap.io");
                    $email->setTo($emailUser);
                    $email->setSubject("Responsable de Tarea");
                    $email->setMessage("Se te ha asignado como responsable en una subtarea.<br><a href='".base_url()."/subtarea/procesarresponsable/".$res[0]["idTareaCompartida"]."/1/1'>Aceptar</a><a href='".base_url()."/subtarea/procesarresponsable/".$res[0]["idTareaCompartida"]."/2/1'>Rechazar</a>");
                    $emailRes=$email->send();
                    while(!$emailRes){
                        $emailRes=$email->send();
                    }
                }
            }else{
                $idTC=$tc->insertNewTCSubTarea($idUser,"3",session()->get("ids")[$idTarea-1],$idSubTarea);
                $tc=null;
                if($idTC){
                    $email=\Config\Services::email();
                    $email->setFrom("correoprueba.proyectos99@gmail.com","IntegradorTyH","rechazados@inbox.mailtrap.io");
                    $email->setReplyTo("respuestas@inbox.mailtrap.io");
                    $email->setTo($emailUser);
                    $email->setSubject("Responsable de SubTarea");
                    $email->setMessage("Se te ha asignado como responsable en una subtarea.<br><a href='".base_url()."/subtarea/procesarresponsable/".$idTC."/1'>Aceptar</a><a href='".base_url()."/subtarea/procesarresponsable/".$idTC."/2'>Rechazar</a>");
                    $emailRes=$email->send();
                    while(!$emailRes){
                        $emailRes=$email->send();
                    }
                    return 1;
                }else{
                    return 0;
                }
            }
        }catch(Error $e){
            return 0;
        }
    }

    private function todasLasSubTareas($id){
        try{
            $tareaModel=new TareaModel();
            $datos["tarea_subTareas"] = $tareaModel->todasLasSubTareas(session()->get("ids")[$id-1],$this->getOrden());
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
            return null;;
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
            case 2: return "Order By prioridad DESC";
            case 3: return "Order By fechaVencimiento DESC";
            default: return "Order By id DESC";
        }
    }

    public function setEstadoTarea($id){
        try{
            if(isset(session()->get("ids")[$id-1])){
                $tareaModel=new TareaModel();
                $estado=$tareaModel->getEstadoTarea(session()->get("ids")[$id-1]);
                if($estado!=null){
                    switch($estado["estadoTarea"]){
                        case 1: if(!$tareaModel->updateEstadoTarea(session()->get("ids")[$id-1],"2")){
                                    $tareaModel=null;
                                    return redirect()->to(base_url()."tarea/".$id)->with("error",["mensaje"=>"No se pudo modificar el estado de la Tarea<br>Intente nuevamente en unos minutos"]);
                                }
                                $tareaModel=null;
                                return redirect()->to(base_url()."tarea/".$id)->with("success",["mensaje"=>"Estado modificado con exito"]);
                        case 2: $datos = $tareaModel->isValidFinalizarTarea(session()->get("ids")[$id-1]);
                                if(!isset($datos[0]["totalFinalizadas"])||!isset($datos[0]["totalSubTareas"])){
                                    $tareaModel=null;
                                    return redirect()->to(base_url()."tarea/".$id)->with("mensajeTarea",["error"=>"","mensaje"=>"No se pudo modificar el estado de la Tarea<br>Intente nuevamente en unos minutos"]);
                                }elseif($datos[0]["totalFinalizadas"]!=$datos[0]["totalSubTareas"]){
                                    $tareaModel=null;
                                    return redirect()->to(base_url()."tarea/".$id)->with("mensajeTarea",["error"=>"","mensaje"=>"No se pudo modificar el estado de la Tarea<br>Primero finalice todas las subTareas"]);
                                }elseif(!$tareaModel->updateEstadoTarea(session()->get("ids")[$id-1],"3")){
                                    $tareaModel=null;
                                    return redirect()->to(base_url()."tarea/".$id)->with("mensajeTarea",["error"=>"","mensaje"=>"No se pudo modificar el estado de la Tarea<br>Intente nuevamente en unos minutos"]);
                                }
                                $tareaModel=null;
                                $tareaComp=new TareaCompartidaModel();
                                $res=$tareaComp->finalizarTC(session()->get("ids")[$id-1]);
                                if(!$res){
                                    $i=0;
                                    while(!$res&&$i<10){
                                        $res=$tareaComp->finalizarTC(session()->get("ids")[$id-1]);
                                        $i++;
                                    }
                                    if(!$res){
                                        return redirect()->to(base_url()."tarea/".$id)->with("mensajeTarea",["error"=>"","mensaje"=>"Tarea finalizada con exito.<br>No se pudo finalizar la tarea para los usuarios compartidos"]);
                                    }
                                }
                                return redirect()->to(base_url()."tarea/".$id)->with("mensajeTarea",["success"=>"","mensaje"=>"Tarea finalizada modificado con exito"]);
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
            $tarea=new TareaModel;
            if(!$tarea->archivarTarea(session()->get("ids")[$id-1])){
                return redirect()->to("tarea/".$id)->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar archivar la tarea.<br>Intente nuevamente en unos minutos."]);
            }
            $idTarea=session()->get("ids")[$id-1];
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
                $tarea=new TareaModel();
                if($tarea->updateTarea(session()->get("ids")[$id-1],
                                   $post["tituloTarea"],
                              $post["descripcionTarea"],
                                $post["prioridadTarea"],
                                    $post["colorTarea"],
                        $post["fechaRecordatorioTarea"])){
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

    public function anexTarea($id){
        try{
            if(isset(session()->get("ids")[$id-1])){
                helper("spanishErrors_helper");
                $post=$this->request->getPost(["descripcionTarea"]);
                $validacion = service('validation');
                $reglas=[
                    "descripcionTarea" =>"required|valid_alphanum_space_punct|max_length[255]|min_length[10]"
                ];
                $validacion->setRules($reglas,spanishErrorMessages($reglas));
                if (!$validacion->withRequest($this->request)->run())
                {
                    return redirect()->to(base_url()."tarea/".$id)->withInput();
                }
                $tarea=new TareaModel();
                $viejaDesc=$tarea->getDescripcionTarea(session()->get("ids")[$id-1]);
                if(!isset($viejaDesc)){
                    return redirect()->to(base_url().'tarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la tarea<br>Intente nuevamente en unos minutos"]);
                }
                if(empty($viejaDesc)){
                    return redirect()->to(base_url().'tarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la tarea<br>Intente nuevamente en unos minutos"]);
                }
                if(!$tarea->anexarTarea(session()->get("ids")[$id-1],$viejaDesc["descripcionTarea"],$post["descripcionTarea"])){
                    $tarea=null;
                    return redirect()->to(base_url().'tarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la tarea<br>Intente nuevamente en unos minutos"]);
                }
                $tarea=null;
                return redirect()->to(base_url().'tarea/'.$id)->with("mensaje",["success"=> "", "mensaje" => "Tarea modificada!"]);
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
            $data=$user->getIdEmailUser($post["usuarioCompartirTarea"]);
            $user=null;
            if(empty($data)){
                $mensaje["usuarioCompartirTarea"]="El usuario ingresado no existe.";
            }elseif($data[0]["idUsuario"]==session()->get("usuario")["id"]){
                $mensaje["usuarioCompartirTarea"]="Ingrese un usuario distinto del suyo.";
            }
            if(!empty($mensaje)){
                return redirect()->back()->withInput()->with('mensaje',$mensaje);
            }
            $tc=new TareaCompartidaModel();
            $res=$tc->getTCTarea(session()->get("ids")[$post["idTarea"]-1],$data[0]["idUsuario"]);
            if(!empty($res)){
                if($res[0]["tipoTareaCompartida"]!=$post["accesibilidadCompartirTarea"] && $res[0]["estadoTareaCompartida"]==1){
                    if(!$tc->updateTCTarea(session()->get("ids")[$post["idTarea"]-1],$data[0]["idUsuario"],$post["accesibilidadCompartirTarea"])){
                        $tc=null;
                        return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "El usuario ya a aceptado la invitacion previamente<br>No se pudo modificar la accesibilidad. Intente nuevamente en unos minutos"]);
                    }
                }elseif($res[0]["estadoTareaCompartida"]==1){
                    $tc=null;
                    return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "El usuario ya a aceptado la invitación previamente"]);
                }elseif($res[0]["estadoTareaCompartida"]==0){
                    $tc=null;
                    return redirect()->to(base_url().'tarea/'.$post["idTarea"])->with("mensaje",["error"=> "", "mensaje"=> "La invitación ya a sido enviada previamente<br>El usuario aun tiene que aceptarla"]);
                }
            }
            $idTC=$tc->insertNewTCTarea($data[0]["idUsuario"],
                                        $post["accesibilidadCompartirTarea"],
                                       session()->get("ids")[$post["idTarea"]-1]);
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
            $res=$tc->getEstadoTC($idTC);
            if(!isset($res)){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }elseif(empty($res)){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }elseif($res["estadoTareaCompartida"]!=0){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Esta invitacion ya a sido procesada"]);
            }
            if(!$tc->updateEstadoTC($idTC,$estado)){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }
            return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"La invitacion se proceso con exito"]);
        }catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
        }
    }

    public function eliminarTarea(){
        try{
            $idTarea=$this->request->getPost("idTarea");
            if(isset($idTarea)){
                if(isset(session()->get("ids")[$idTarea-1])){
                    $trueIdTarea=session()->get("ids")[$idTarea-1];
                }else{
                    return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar acceder a la tarea"]);
                }
                $comentarioModel=new ComentarioModel();
                if(!$comentarioModel->deleteAllComentariosFromTarea($trueIdTarea)){
                    $comentarioModel=null;
                    return redirect()->to(base_url()."tarea/".$idTarea)->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentear eliminar los comentarios dentro de la tarea.<br>Intente nuevamente en unos minutos."]);
                }
                $comentarioModel=null;
                $subTareaModel=new SubTareaModel();
                if(!$subTareaModel->deleteSubTareasTarea($trueIdTarea)){
                    $subTareaModel=null;
                    return redirect()->to(base_url()."tarea/".$idTarea)->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentear eliminar las subTareas de la tarea.<br>Intente nuevamente en unos minutos."]);
                }
                $subTareaModel=null;
                $tareaModel=new TareaModel();
                if(!$tareaModel->deleteTarea($trueIdTarea)){
                    $tareaModel=null;
                    return redirect()->to(base_url()."tarea/".$idTarea)->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentear eliminar la tarea.<br>Intente nuevamente en unos minutos."]);
                }
                $tareaModel=null;
                $TCModel=new TareaCompartidaModel();
                $res=$TCModel->deleteTCsTarea($trueIdTarea);
                if(!$res){
                    $i=0;
                    while(!$res && $i<10){
                        $res=$TCModel->deleteTCsTarea($trueIdTarea);
                        $i++;
                    }
                    if(!$res){
                        return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Tarea eliminada con exito.<br>No se pudieron eliminar las relaciones de la tarea"]);
                    }
                }
                return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"Tarea eliminada con exito"]);
            }
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar obtener la tarea."]);
        }catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello."]);
        }

    }
}