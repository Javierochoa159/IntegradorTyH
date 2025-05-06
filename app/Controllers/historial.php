<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\UsuarioModel;
use App\Models\SubTareaModel;
use App\Models\ComentarioModel;
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
            $db = \Config\Database::connect();
            $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.descripcionTarea AS descripcion, tareas.prioridadTarea AS prioridad, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, tareas.autorTarea AS autor, "tarea" AS tarea_subtarea
                                        FROM tareas
                                        LEFT JOIN tareasCompartidas ON tareasCompartidas.idTarea=tareas.idTarea
                                        WHERE tareas.idTarea = '.session()->get("idsUsuario")[$id-1].'
                                              AND tareas.tareaArchivada = 1
                                              AND tareas.autorTarea = '.session()->get("usuario")["id"].'
                                        UNION
                                            SELECT subTareas.idSubTarea AS id, "" AS titulo, subTareas.descripcionSubTarea AS descripcion, subTareas.prioridadSubTarea AS prioridad, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, "" AS fechaRecordatorio, subTareas.colorSubTarea AS color, subTareas.autorSubTarea AS autor, "subtarea" AS tarea_subtarea
                                            FROM subTareas
                                            WHERE subTareas.idTarea = '.session()->get("idsUsuario")[$id-1].'
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
                $sqlIn=[
                    "tituloTarea"=>$post["tituloTarea"],
                    "descripcionTarea"=>$post["descripcionTarea"],
                    "prioridadTarea"=>$post["prioridadTarea"],
                    "colorTarea"=>$post["colorTarea"]
                ];
                if($post["fechaRecordatorioTarea"]!=null) $sqlIn["fechaRecordatorioTarea"]=$post["fechaRecordatorioTarea"];
                $tarea=new TareaModel();
                if($tarea->update(session()->get("idsUsuario")[$id-1],$sqlIn)){
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
}