<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\UsuarioModel;
use Error;

Class Inicio extends BaseController{
    public function __construct(){ 
        helper ('form');
    }
    public function index(){
        if(session()->has("usuario")){
            try{
                $userModel=new UsuarioModel();
                $user=$userModel->getIdUsuario();
                if(empty($user)){
                    session()->destroy();
                    redirect()->to(base_url()."login");
                }else{
                    switch(session()->get("pagina")){
                        case 1: $datos=$this->todasLasTareas(); break;
                        case 2: $datos=$this->todasMisTareasActivas(); break;
                        case 3: $datos=$this->todasLasTareasCompartidas(); break;
                        case 4: $datos=$this->todasMisTareas(); break;
                        default: $datos=$this->todasLasTareas();
                    }
                    return view("InicioView",$datos);
                }
            }catch(Error $e){
                return redirect()->to(base_url()."login");
            }
        }
        return redirect()->to(base_url()."login");
    }

    public function newTarea(){
        try{
            helper("spanishErrors_helper");
            $post=$this->request->getPost(["tituloTarea","descripcionTarea","prioridadTarea","colorTarea","fechaVencimientoTarea","fechaRecordatorioTarea"]);
            $validacion = service('validation');
            $reglas=[
                "tituloTarea" =>"required|valid_alphanum_space|max_length[30]|min_length[6]",
                "descripcionTarea" =>"required|valid_alphanum_space_punct|max_length[255]|min_length[10]",
                "fechaVencimientoTarea" =>"required|valid_date"
            ];
            if($post["fechaRecordatorioTarea"]!=null) $reglas["fechaRecordatorioTarea"]="valid_date";
            $validacion->setRules($reglas,spanishErrorMessages($reglas));
            if (!$validacion->withRequest($this->request)->run())
            {
                return redirect()->to(base_url()."inicio")->withInput();
            }
            $tarea=new TareaModel();
            if($tarea->insertNewTarea($post["tituloTarea"],
                                 $post["descripcionTarea"],
                                   $post["prioridadTarea"],
                                       $post["colorTarea"],
                            $post["fechaVencimientoTarea"],
                           $post["fechaRecordatorioTarea"])){
                $tarea=null;
                return redirect()->to(base_url().'inicio')->with("mensaje",["success"=> "", "mensaje" => "Tarea creada!"]);
            }else{
                $tarea=null;
                return redirect()->to(base_url().'inicio')->with("mensaje",["error"=> "", "mensaje"=> "Error al crear la tarea<br>Intente nuevamente en unos minutos"]);
            }
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }

    public function todas($orden=1){
        session()->set("pagina",1);
        $this->setOrden($orden);
        return redirect()->to(base_url()."inicio");
    }
    public function misTareas($orden=1){
        session()->set("pagina",2);
        $this->setOrden($orden);
        return redirect()->to(base_url()."inicio");
    }
    public function tareasCompartidas($orden=1){
        session()->set("pagina",3);
        $this->setOrden($orden);
        return redirect()->to(base_url()."inicio");
    }
    public function historial($orden=1){
        session()->set("pagina",4);
        $this->setOrden($orden);
        return redirect()->to(base_url()."inicio");
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
            case 2: return "ORDER BY prioridadOrdenada DESC, fechaVencimiento ASC";
            case 3: return "Order By fechaVencimiento";
            default: return "Order By id DESC";
        }
    }
    private function todasLasTareas(){
        try{
            $tareaModel=new TareaModel();
            $datos["tareas"]= $tareaModel->getTodasLasTareas($this->getOrden());
            $aux=[];
            foreach($datos["tareas"] as $tarea){
                $aux[]=$tarea["id"];
                if(isset($tarea["fechaRecordatorio"]) && isset($tarea["recoNotify"])){
                    if(date("U")-date_format(date_create($tarea["fechaRecordatorio"]),"U")>=0 && $tarea["recoNotify"]==0){
                        $this->notificarRecordatorio($tarea);
                    }
                }
            }
            session()->set("ids",$aux);
            $datos["ids"]=$aux;
            return $datos;
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }
    private function notificarRecordatorio($tarea){
        $user=new UsuarioModel();
        $userEmail=$user->getEmailUser($tarea["autor"]);
        $user=null;
        if(isset($userEmail)){
            if(!empty($userEmail)){
                $email=\Config\Services::email();
                $email->setFrom("correoprueba.proyectos99@gmail.com","IntegradorTyH","rechazados@inbox.mailtrap.io");
                $email->setReplyTo("respuestas@inbox.mailtrap.io");
                $email->setTo($userEmail["emailUsuario"]);
                $email->setSubject("Recordatorio Tarea");
                $email->setMessage("La fecha de vencimiento de la tarea '".$tarea["titulo"]."' se acerca.<br>Vence el ".substr($tarea["fechaVencimiento"],0,-3));
                if($email->send()){
                    $tareas=new TareaModel();
                    if($tareas->recordatorioTarea($tarea["id"],true)){
                        $tarea["recoNotify"]=1;
                    }
                    $tareas=null;
                }
            }
        }
    }
    private function todasMisTareasActivas(){
        try{
            $tareaModel=new TareaModel();
            $datos["tareas"] = $tareaModel->todasMisTareasActivas($this->getOrden());
            $aux=[];
            foreach($datos["tareas"] as $tarea){
                $i=array_find_key(session()->get("ids"),function($value)use($tarea){return $tarea["id"]===$value;});
                $aux[$i]=$tarea["id"];
            }
            $datos["ids"]=$aux;
            $datos["pagina"]=2;
            return $datos;
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }
    public function todasLasTareasCompartidas(){
        try{
            $tareaModel=new TareaModel();
            $datos["tareas"] = $tareaModel->todasLasTareasCompartidas($this->getOrden());
            $aux=[];
            foreach($datos["tareas"] as $tarea){
                $i=array_find_key(session()->get("ids"),function($value)use($tarea){return $tarea["id"]===$value;});
                $aux[$i]=$tarea["id"];
            }
            $datos["ids"]=$aux;
            $datos["pagina"]=3;
            return $datos;
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }

    private function todasMisTareas(){
        try{
            $tareaModel=new TareaModel();
            $datos["tareas"] = $tareaModel->todasMisTareas($this->getOrden());
            $aux=[];
            foreach($datos["tareas"] as $tarea){
                $aux[]=$tarea["id"];
            }
            session()->set("idsUsuario",$aux);
            $datos["ids"]=$aux;
            $datos["pagina"]=4;
            return $datos;
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello"]);
        }
    }

    public function tarea($id){
        session()->set("orden",1);
        if(session()->get("pagina")!=4){
            session()->set("pagina",1);
            session()->set("orden",1);
            if(isset(session()->get("ids")[$id-1]))
                return redirect()->to(base_url()."tarea/".$id);
            else return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al ingresar a la tarea"]);
        }
        elseif(session()->get("pagina")==4){
            session()->set("pagina",1);
            if(isset(session()->get("idsUsuario")[$id-1]))
                return redirect()->to(base_url()."historial/".$id);
            else return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al ingresar a la tarea"]);
        }
        else return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello"]);
    }
    public function subTarea($id){
        session()->set("orden",1);
        if(session()->get("pagina")==1||session()->get("pagina")==2){
            session()->set("pagina",1);
            if(isset(session()->get("ids")[$id-1])){
                session()->set("subTareaShare",[$id=>session()->get("ids")[$id-1]]);
                return redirect()->to(base_url()."subtarea/".$id);
            }
            else return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al ingresar a la subtarea"]);
        }
        else return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello"]);
    }

    public function logout(){
        session()->destroy();
        return redirect()->to(base_url()."login");
    }
}

