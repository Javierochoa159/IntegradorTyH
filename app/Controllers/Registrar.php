<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

Class Registrar extends BaseController{
    private $encriptado;
    public function __construct(){ 
        helper ('form');
    }
    public function index(){
        return view("RegistrarView");
    }
    public function exito(){
        helper("spanishErrors_helper");
        $post=$this->request->getPost(['user',"email","pass"]);
        $validacion = service('validation');
        $reglas=[
            "user" => "required|min_length[4]|max_length[15]|valid_alphanum_dash",
            "email" => "required|min_length[5]|max_length[255]|valid_email",
            "pass" => "required|min_length[8]|max_length[16]|valid_pass"
        ];
        $validacion->setRules($reglas,spanishErrorMessages($reglas));
        if (!$validacion->withRequest($this->request)->run())
        {
            return redirect()->to(base_url()."registrar")->withInput();
        }
        $user=new UsuarioModel();
        $mensaje=array();
        $data=$user->getUsuarioAtUserName($post["user"]);
        if(!empty($data)){
            $mensaje["user"]= "El usuario ingresado ya está en uso.";
        }
        $data=$user->getIdUserAtEmail($post["email"]);
        if(!empty($data)){
            $mensaje["email"]= "El correo ingresado ya está en uso.";
        }
        if(!empty($mensaje)){
            $user=null;
            return redirect()->to(base_url()."registrar")->withInput()->with('errors',$mensaje);
        }else{
            $this->encriptado=\Config\Services::encrypter();
            $userId=$user->insertNewUser(
                $post["user"],
                $post["email"],
                base64_encode($this->encriptado->encrypt($post["pass"])));
            $post["pass"]="";
            if(!$userId){
                $user=null;
                return redirect()->to(base_url()."registrar")->with('errors',["errorRegistrar"=>"Ocurrio un error al crear la cuenta<br>Intente nuevamente despues de unos minutos."]);
            }
            $user=["id"=>$userId,"user"=>$post["user"]];
            session()->set("usuario", $user);
            return redirect()->to(base_url().'login'); 
        }
    }
}
