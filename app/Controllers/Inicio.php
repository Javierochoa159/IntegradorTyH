<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\UsuarioModel;
use Error;

Class Inicio extends BaseController{
    public function __construct(){ 
        helper ('form');
        session()->set("orden",1);
    }
    public function index(){
        if(session()->has("usuario")){
            try{
                $user=new UsuarioModel();
                $user->select("idUsuario")->find(session()->get("usuario")["id"]);
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
            $sqlIn=[
                "tituloTarea"=>$post["tituloTarea"],
                "descripcionTarea"=>$post["descripcionTarea"],
                "prioridadTarea"=>$post["prioridadTarea"],
                "colorTarea"=>$post["colorTarea"],
                "fechaVencimientoTarea"=>$post["fechaVencimientoTarea"],
                "autorTarea"=> session()->get("usuario")["id"]
            ];
            if($post["fechaRecordatorioTarea"]!=null) $sqlIn["fechaRecordatorioTarea"]=$post["fechaRecordatorioTarea"];
            $tarea=new TareaModel();
            if($tarea->insert($sqlIn)){
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
            case 2: return "Order By fechaVencimiento";
            case 3: return "Order By prioridad";
        }
    }
    private function todasLasTareas(){
        try{
            $db = \Config\Database::connect();
            $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.prioridadTarea AS prioridad, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, "tarea" AS tarea_subtarea
                                            FROM tareas
                                            LEFT JOIN tareasCompartidas ON tareasCompartidas.idTarea=tareas.idTarea
                                            WHERE    (tareas.autorTarea = '.session()->get("usuario")["id"].' AND tareas.tareaArchivada = 0)
                                                  OR (tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].' AND tareasCompartidas.estadoTareaCompartida = "1")
                                            UNION
                                                SELECT subTareas.idSubTarea AS id, subTareas.descripcionSubTarea AS titulo, subTareas.prioridadSubTarea AS prioridad, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, "" AS fechaRecordatorio, subTareas.colorSubTarea AS color, "subtarea" AS tarea_subtarea
                                                FROM subTareas
                                                LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea=subTareas.idSubTarea
                                                WHERE tareasCompartidas.estadoTareaCompartida="1"
                                                      AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].'
                                            ';
            $sql.=$this->getOrden();
            $query   = $db->query($sql);
            $datos["tareas"] = $query->getResultArray();
            $db->close();
            $aux=[];
            foreach($datos["tareas"] as $tarea){
                $aux[]=$tarea["id"];
            }
            session()->set("ids",$aux);
            $datos["ids"]=$aux;
            return $datos;
        }
        catch(Error $e){
            return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado<br>Estamos trabajando en ello"]);
        }
    }
    private function todasMisTareasActivas(){
        try{
            $db = \Config\Database::connect();
            $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.prioridadTarea AS prioridad, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, "tarea" AS tarea_subtarea
                                            FROM tareas
                                            WHERE tareas.autorTarea = '.session()->get("usuario")["id"].'
                                                  AND tareas.tareaArchivada = 0
                                            ';
            $sql.=$this->getOrden();
            $query   = $db->query($sql);
            $datos["tareas"] = $query->getResultArray();
            $db->close();
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
            $db = \Config\Database::connect();
            $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.prioridadTarea AS prioridad, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, "tarea" AS tarea_subtarea
                                        FROM tareas
                                        LEFT JOIN tareasCompartidas ON tareasCompartidas.idTarea=tareas.idTarea
                                        WHERE tareasCompartidas.estadoTareaCompartida = "1"
                                              AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].'
                                        UNION
                                            SELECT subTareas.idSubTarea AS id, subTareas.descripcionSubTarea AS titulo, subTareas.prioridadSubTarea AS prioridad, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, "" AS fechaRecordatorio, subTareas.colorSubTarea AS color, "subtarea" AS tarea_subtarea
                                            FROM subTareas
                                            LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea=subTareas.idSubTarea
                                            WHERE tareasCompartidas.estadoTareaCompartida = "1"
                                                  AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].'
                                        ';
            $sql.=$this->getOrden();
            $query   = $db->query($sql);
            $datos["tareas"] = $query->getResultArray();
            $db->close();
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
            $db = \Config\Database::connect();
            $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.prioridadTarea AS prioridad, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, "tarea" AS tarea_subtarea
                                            FROM tareas
                                            WHERE tareas.autorTarea = '.session()->get("usuario")["id"].'
                                                  AND tareas.tareaArchivada = 1
                                            ';
            $sql.=$this->getOrden();
            $query   = $db->query($sql);
            $datos["tareas"] = $query->getResultArray();
            $db->close();
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
        if(session()->get("pagina")!=4){
            session()->set("pagina",1);
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
        if(session()->get("pagina")!=4){
            session()->set("pagina",1);
            session()->set("subTareaShare",session()->get("ids")[$id-1]);
            if(isset(session()->get("ids")[$id-1]))
                return redirect()->to(base_url()."subtarea/".$id);
            else return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error al ingresar a la subtarea"]);
        }
        else return redirect()->to(base_url()."inicio")->with("mensaje",["error"=>"","mensaje"=>"Ocurrio un error inesperado. Estamos trabajando en ello"]);
    }

    public function logout(){
        session()->destroy();
        return redirect()->to(base_url()."login");
    }
}

