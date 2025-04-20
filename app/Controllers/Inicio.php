<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\TareaCompartidaModel;

Class Inicio extends BaseController{
    public function __construct(){ 
        helper ('form');
    }
    public function index(){
        if(session()->has("usuario")){
            $tarea = new TareaModel;
            $datos["tareas"]=$tarea->select(["tituloTarea", "descripcionTarea", "prioridadTarea", "estadoTarea", "fechaVencimientoTarea", "fechaRecordatorioTarea", "colorTarea"])->where("autorTarea",session()->get("usuario")["id"])->whereNotIn("estadoTarea",[3])->orderBy("idTarea","desc")->find();
            $tarea=null;
            return view("InicioView",$datos);
        }
        return redirect()->to("/public/index.php/login");
    }

    public function newTarea(){
        helper("spanishErrors_helper");
        $post=$this->request->getPost(["tituloTarea","descripcionTarea","prioridadTarea","colorTarea","fechaVencimientoTarea","fechaRecordatorioTarea"]);
        $validacion = service('validation');
        $reglas=[
            "tituloTarea" =>"required|valid_alphanum_space|max_length[30]|min_length[6]",
            "descripcionTarea" =>"required|valid_alphanum_space|max_length[255]|min_length[10]",
            "fechaVencimientoTarea" =>"required|valid_date"
        ];
        $validacion->setRules($reglas,spanishErrorMessages($reglas));
        if (!$validacion->withRequest($this->request)->run())
        {
            return redirect()->to("/public/index.php/inicio")->withInput();
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
            return redirect()->to('public/index.php/inicio')->with("mensaje",["success"=>"Tarea creada!"]);
        }else{
            return redirect()->to('public/index.php/inicio')->with("mensaje",["error"=>"Error al crear la tarea<br>Intente nuevamente en unos minutos"]);
        }
    }

    public function logout(){
        session()->destroy();
        return redirect()->to("/public/index.php/login");
    }
}

