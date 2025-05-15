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
            $subTareaModel=new SubTareaModel();
            $datos["subTarea_comentarios"] = $subTareaModel->todosLosComentarios($id);
            $subTareaModel=null;
            return $datos;
        }
        catch(Error $e){
            return null;
        }
    }

    public function newComentario(){
        try{
            if(!isset(session()->get("idsTarea")[$this->request->getPost("idSubTarea")-1]) && !isset(session()->get("subTareaShare")[$this->request->getPost("idSubTarea")])){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado al momento de crear un Comentario<br>Intentelo nuevamente en unos minutos"]);
            }else{
                if(isset(session()->get("idsTarea")[$this->request->getPost("idSubTarea")-1])){
                    $idSubTarea=session()->get("idsTarea")[$this->request->getPost("idSubTarea")-1];
                }else{
                    $idSubTarea=session()->get("subTareaShare")[$this->request->getPost("idSubTarea")];
                }
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
                    $comentarioModel=new ComentarioModel();
                    if(!$comentarioModel->insertNewComentario($idSubTarea,$post["comentarioComentario"])){
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
            if(!isset(session()->get("idsTarea")[$id-1]) && !isset(session()->get("subTareaShare")[$id])){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello."]);
            }else{
                if(isset(session()->get("idsTarea")[$id-1])){
                    $idSubTarea=session()->get("idsTarea")[$id-1];
                }else{
                    $idSubTarea=session()->get("subTareaShare")[$id];
                }
                helper("spanishErrors_helper");
                $post=$this->request->getPost(["descripcionSubTarea","prioridadSubTarea","colorSubTarea"]);
                $validacion = service('validation');
                $reglas=[
                    "descripcionSubTarea" =>"required|valid_alphanum_space_punct|max_length[255]|min_length[10]"
                ];
                $validacion->setRules($reglas,spanishErrorMessages($reglas));
                if (!$validacion->withRequest($this->request)->run())
                {
                    return redirect()->to(base_url()."subtarea/".$id)->withInput();
                }
                $subTarea=new SubTareaModel();
                if(!$subTarea->updateSubTarea($idSubTarea,
                                     $post["descripcionSubTarea"],
                                       $post["prioridadSubTarea"],
                                           $post["colorSubTarea"])){
                    $subTarea=null;
                    return redirect()->to(base_url().'subtarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la subTarea<br>Intente nuevamente en unos minutos"]);
                }
                $subTarea=null;
                return redirect()->to(base_url().'subtarea/'.$id)->with("mensaje",["success"=> "", "mensaje" => "SubTarea modificada!"]);
            }
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }
    public function anexarSubTarea($id){
        try{
            if(!isset(session()->get("idsTarea")[$id-1]) && !isset(session()->get("subTareaShare")[$id])){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello."]);
            }else{
                if(isset(session()->get("idsTarea")[$this->request->getPost("idSubTarea")-1])){
                    $idSubTarea=session()->get("idsTarea")[$this->request->getPost("idSubTarea")-1];
                }else{
                    $idSubTarea=session()->get("subTareaShare")[$this->request->getPost("idSubTarea")];
                }
                helper("spanishErrors_helper");
                $post=$this->request->getPost(["descripcionSubTarea"]);
                $validacion = service('validation');
                $reglas=[
                    "descripcionSubTarea" =>"required|valid_alphanum_space_punct|max_length[255]|min_length[10]"
                ];
                $validacion->setRules($reglas,spanishErrorMessages($reglas));
                if (!$validacion->withRequest($this->request)->run())
                {
                    return redirect()->to(base_url()."subtarea/".$id)->withInput();
                }
                $subTarea=new SubTareaModel();
                $viejaDesc=$subTarea->getDescripcionSubTarea($idSubTarea);
                if(!isset($viejaDesc)){
                    return redirect()->to(base_url().'subtarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la subTarea<br>Intente nuevamente en unos minutos"]);
                }
                if(empty($viejaDesc)){
                    return redirect()->to(base_url().'subtarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la subTarea<br>Intente nuevamente en unos minutos"]);
                }
                if(!$subTarea->anexarSubTarea($idSubTarea,$viejaDesc["descripcionSubTarea"],$post["descripcionSubTarea"])){
                    $subTarea=null;
                    return redirect()->to(base_url().'subtarea/'.$id)->with("mensaje",["error"=> "", "mensaje"=> "Error al modificar la subTarea<br>Intente nuevamente en unos minutos"]);
                }
                $subTarea=null;
                return redirect()->to(base_url().'subtarea/'.$id)->with("mensaje",["success"=> "", "mensaje" => "SubTarea modificada!"]);
            }
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello"]);
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
                $estado=$subtarea->getEstadoSubTarea($idSubTarea);
                if($estado!=null){
                    switch($estado["estadoSubTarea"]){
                        case 1: if(!$subtarea->updateEstadoSubTarea($idSubTarea,"2")){
                                    $subtarea=null;
                                    return redirect()->to(base_url()."subtarea/".$id)->with("error",["mensaje"=>"No se pudo modificar el estado de la SubTarea<br>Intente nuevamente en unos minutos"]);
                                }
                                $subtarea=null;
                                $tareaModel=new TareaModel();
                                $idTarea=$tareaModel->getIdTareaSubTarea($idSubTarea);
                                if(isset($idTarea[0]["idTarea"])){
                                    $estadoTarea=$tareaModel->getEstadoTarea($idTarea[0]["idTarea"]);
                                    if(isset($estadoTarea["estadoTarea"])){
                                        if($estadoTarea["estadoTarea"]==1){
                                            $res=$tareaModel->updateEstadoTarea($idTarea[0]["idTarea"],"2");
                                            if(!$res){
                                                $i=0;
                                                while(!$res && $i<10){
                                                    $res=$tareaModel->updateEstadoTarea($idTarea[0]["idTarea"],"2");
                                                    $i++;
                                                }
                                                if(!$res){
                                                    return redirect()->to(base_url()."subtarea/".$id)->with("error",["mensaje"=>"El estado de la SubTarea fue modificado con exito<br>Ocurrio un error al actualizar el estado de la Tarea"]);
                                                }
                                            }
                                        }
                                        return redirect()->to(base_url()."subtarea/".$id)->with("success",["mensaje"=>"Estado modificado con exito"]);
                                    }else{
                                        return redirect()->to(base_url()."subtarea/".$id)->with("error",["mensaje"=>"El estado de la SubTarea fue modificado con exito<br>Ocurrio un error al actualizar el estado de la Tarea"]);
                                    }
                                }else{
                                    return redirect()->to(base_url()."subtarea/".$id)->with("error",["mensaje"=>"El estado de la SubTarea fue modificado con exito<br>Ocurrio un error al actualizar el estado de la Tarea"]);
                                }
                        case 2: if(!$subtarea->updateEstadoSubTarea($idSubTarea,"3")){
                                    $subtarea=null;
                                    return redirect()->to(base_url()."subtarea/".$id)->with("error",["mensaje"=>"No se pudo modificar el estado de la SubTarea<br>Intente nuevamente en unos minutos"]);
                                }
                                $idUsuarioSubTarea=$subtarea->getAutorSubTarea($idSubTarea);
                                $subtarea=null;
                                $tareaComp=new TareaCompartidaModel();
                                $res=$tareaComp->finalizarTCSubTarea($idSubTarea);
                                if(!$res){
                                    $i=0;
                                    while(!$res&&$i<10){
                                        $res=$tareaComp->finalizarTCSubTarea($idSubTarea);
                                        $i++;
                                    }
                                    if(!$res){
                                        return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"SubTarea finalizada con exito.<br>No se pudo finalizar la subTarea para los usuarios compartidos"]);
                                    }
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
            $data=$user->getIdEmailUser($post["usuarioCompartirSubTarea"]);
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
            $idTarea=$tarea->getIdTareaSubTarea(session()->get("idsTarea")[$post["idSubTarea"]-1]);
            if(empty($idTarea)){
                return redirect()->to("subTarea/".$post["idSubTarea"])->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar compartir la subTarea.<br>Intente nuevamente en unos minutos."]);
            }
            $tc=new TareaCompartidaModel();
            $res=$tc->getTCSubTarea($idTarea[0]["idTarea"],
                                     $data[0]["idUsuario"],
                                 session()->get("idsTarea")[$post["idSubTarea"]-1]);
            if(!empty($res)){
                if($res[0]["tipoTareaCompartida"]!=$post["accesibilidadCompartirSubTarea"] && $res[0]["estadoTareaCompartida"]==1){
                    if(!$tc->updateTCSubTarea($res[0]["idTareaCompartida"],$post["accesibilidadCompartirSubTarea"])){
                        $tc=null;
                        return redirect()->to(base_url().'subtarea/'.$post["idSubTarea"])->with("mensaje",["error"=> "", "mensaje"=> "El usuario ya a aceptado la invitacion previamente<br>No se pudo modificar la accesibilidad. Intente nuevamente en unos minutos"]);
                    }
                }elseif($res[0]["estadoTareaCompartida"]==1){
                    $tc=null;
                    return redirect()->to(base_url().'subtarea/'.$post["idSubTarea"])->with("mensaje",["error"=> "", "mensaje"=> "El usuario ya a aceptado la invitación previamente"]);
                }elseif($res[0]["estadoTareaCompartida"]==0){
                    $tc=null;
                    return redirect()->to(base_url().'subtarea/'.$post["idSubTarea"])->with("mensaje",["error"=> "", "mensaje"=> "La invitación ya a sido enviada previamente<br>El usuario aun tiene que aceptarla"]);
                }
            }
            $idTC=$tc->insertNewTCSubTarea($data[0]["idUsuario"],
                                           $post["accesibilidadCompartirSubTarea"],
                                          $idTarea[0]["idTarea"],
                                       session()->get("idsTarea")[$post["idSubTarea"]-1]);
            $tc=null;
            if($idTC){
                $email=\Config\Services::email();
                $email->setFrom("correoprueba.proyectos99@gmail.com","IntegradorTyH","rechazados@inbox.mailtrap.io");
                $email->setReplyTo("respuestas@inbox.mailtrap.io");
                $email->setTo($data[0]["emailUsuario"]);
                $email->setSubject("Invitacion a SubTarea");
                $email->setMessage("Has recibido una invitacion a una subtarea en tu cuenta<br><a href='".base_url()."/subtarea/procesarshare/".$idTC."/1'>Aceptar</a><a href='".base_url()."/subtarea/procesarshare/".$idTC."/2'>Rechazar</a>");
                if(!$email->send()){
                    $i=0;
                    while(!$email->send() && $i<10){
                        $i++;
                    }
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
    public function procesarResponsable($idTC, $estado, $estadoTC=0){
        try{
            if(($estado!=1 && $estado!=2) || ($estadoTC!=0 && $estadoTC!=1 && $estadoTC!=2)){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }
            $tc=new TareaCompartidaModel();
            $res=$tc->getEstadoTC($idTC);
            if(empty($res)){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
            }elseif($res["estadoTareaCompartida"]!=0){
                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Esta invitacion ya a sido procesada"]);
            }
            if($estado==1){
                $idSubTarea=$tc->getIdSubTareaIdUserTC($idTC);
                if(!isset($idSubTarea)){
                    return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
                }
                if(empty($idSubTarea)){
                    return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
                }
                switch ($estadoTC){
                case 0: if(!$tc->updateEstadoTC($idTC,$estado)){
                            $tc=null;
                            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
                        }
                        $tc=null;
                        $subTarea=new SubTareaModel();
                        $res=$subTarea->updateResponsableSubTarea($idSubTarea["idSubTarea"],$idSubTarea["idUsuario"]);
                        if(!$res){
                            $i=0;
                            while(!$res && $i<10){
                                $res=$subTarea->updateResponsableSubTarea($idSubTarea["idSubTarea"],$idSubTarea["idUsuario"]);
                                $i++;
                            }
                            if(!$res){
                                $subTarea=null;
                                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"La invitacion se proceso con exito<br>Ocurrio un error al momento de asignar la responsabilidad"]);
                            }
                        }
                        $subTarea=null;
                        return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"La invitacion se proceso con exito"]);
                
                case 1: if(!$tc->updateTCSubTarea($idTC,"3")){
                            $tc=null;
                            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de procesar la invitacion"]);
                        }
                        $tc=null;
                        $subTarea=new SubTareaModel();
                        $res=$subTarea->updateResponsableSubTarea($idSubTarea["idSubTarea"],$idSubTarea["idUsuario"]);
                        if(!$res){
                            $i=0;
                            while(!$res && $i<10){
                                $res=$subTarea->updateResponsableSubTarea($idSubTarea["idSubTarea"],$idSubTarea["idUsuario"]);
                                $i++;
                            }
                            if(!$res){
                                return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al momento de asignar la responsabilidad"]);
                            }
                        }
                        $subTarea=null;
                        return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"La invitacion se proceso con exito"]);
                }
            }else{
                if(!$tc->updateEstadoTC($idTC,$estado)){
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

    public function eliminarSubTarea(){
        try{
            $idSubTarea=$this->request->getPost("idSubTarea");
            if(isset($idSubTarea)){
                if(isset(session()->get("idsTarea")[$idSubTarea-1])){
                    $trueIdSubTarea=session()->get("idsTarea")[$idSubTarea-1];
                }elseif(session()->get("idsUsuario")[$idSubTarea-1]){
                    $trueIdSubTarea=session()->get("idsUsuario")[$idSubTarea-1];
                }else{
                    return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar acceder a la subTarea"]);
                }
                $subTareaModel=new SubTareaModel();
                if(!$subTareaModel->deleteSubTarea($trueIdSubTarea)){
                    $subTareaModel=null;
                    return redirect()->to(base_url()."tarea/".$idSubTarea)->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentear eliminar la subTarea.<br>Intente nuevamente en unos minutos."]);
                }else{
                    $subTareaModel=null;
                    $TCModel=new TareaCompartidaModel();
                    $res=$TCModel->deleteTCsSubTarea($trueIdSubTarea);
                    if(!$res){
                        $i=0;
                        while(!$res && $i<10){
                            $res=$TCModel->deleteTCsSubTarea($trueIdSubTarea);
                            $i++;
                        }
                        if(!$res){
                            $TCModel=null;
                            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"SubTarea eliminada con exito.<br>No se pudieron eliminar las relaciones de la subTarea"]);
                        }
                    }
                    $TCModel=null;
                    return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"SubTarea eliminada con exito"]);
                }
            }
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar obtener la subTarea."]);
        }catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello."]);
        }
    }
}