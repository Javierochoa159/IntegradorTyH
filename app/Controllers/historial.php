<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\TareaCompartidaModel;
use Error;

class Historial extends BaseController{
    public function __construct(){ 
        helper ('form');
        session()->set("orden",1);
    }
    public function index($id){
        if(isset(session()->get("idsUsuario")[$id-1])){
            $datos=$this->todasLasSubTareas($id);
            $datos["urlTarea"]="historial";
            return view("tareaView",$datos);
        }else return redirect()->to(base_url()."inicio")->with("mensaje",["error" => "", "mensaje" => "Tarea no encontrada"]);
    }
    private function todasLasSubTareas($id){
        try{
            $tareaModel=new TareaModel();
            $datos["tarea_subTareas"] = $tareaModel->todasLasSubTareasH(session()->get("idsUsuario")[$id-1],$this->getOrden());
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
        if(isset(session()->get("idsUsuario")[$id-1])){
            $this->setOrden($orden);
            return redirect()->to(base_url()."historial/".$id);
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
            default: return "Order By id DESC";
        }
    }
    public function modTarea($id){
        try{
            if(isset(session()->get("idsUsuario")[$id-1])){
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
                if($tarea->updateTarea(session()->get("idsUsuario")[$id-1],
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
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello"]);
        }
    }

    public function subTarea($id){
        if(!isset(session()->get("idsTarea")[$id-1])) return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al ingresar a la subTarea"]);
        else return redirect()->to(base_url()."subtarea/".$id);
    }

    public function eliminarTarea(){
        try{
            $idTarea=$this->request->getPost("idTarea");
            if(isset($idTarea)){
                if(session()->get("idsUsuario")[$idTarea-1]){
                    $trueIdTarea=session()->get("idsUsuario")[$idTarea-1];
                }else{
                    return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar acceder a la tarea"]);
                }
                $tareaModel=new TareaModel();
                if(!$tareaModel->deleteTarea($trueIdTarea)){
                    $tareaModel=null;
                    return redirect()->to(base_url()."historial/".$idTarea)->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentear eliminar la tarea.<br>Intente nuevamente en unos minutos."]);
                }else{
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
                    $tareaModel=null;
                    return redirect()->to(base_url()."inicio")->with("mensaje",["success"=>"","mensaje"=>"Tarea eliminada con exito"]);
                }
            }
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al intentar obtener la tarea."]);
        }catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello."]);
        }

    }
}