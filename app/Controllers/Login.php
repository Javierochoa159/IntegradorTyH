<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

Class Login extends BaseController{
    private $encriptado;
    public function __construct(){ 
        helper ('form');
    }
    public function index(){
        if(session()->has("usuario")){
            return redirect()->to(base_url()."inicio");
        }
        return view("LoginView");
    }
    public function exito(){
        helper("spanishErrors_helper");
        $post=$this->request->getPost(["email","pass"]);
        $validacion = service('validation');
        $reglas=[
            "email" => "required|valid_email",
            "pass" => "required"
        ];
        $validacion->setRules($reglas,spanishErrorMessages($reglas));
        if (!$validacion->withRequest($this->request)->run())
        {
            return redirect()->to(base_url()."login")->withInput();
        }
        
        $user=new UsuarioModel();
        $mensaje=array();
        $data=$user->getPassUser($post["email"]);
        if(empty($data)){
            $mensaje["email"]= "Correo no encontrado.";
        }else{
            $this->encriptado=\Config\Services::encrypter();
            if($post["pass"]!==$this->encriptado->decrypt(base64_decode($data[0]))){
                $mensaje["pass"]= "Contraseña incorrecta.";
            }
            $post["pass"]=null;
            $data=null;
        }
        if(!empty($mensaje)){
            $user=null;
            return redirect()->to(base_url()."login")->withInput()->with('errors',$mensaje);
        }else{
            $userInfo=$user->getUserInfo($post["email"]);
            $user=null;
            if(empty($userInfo)){
                return redirect()->to(base_url()."login")->with('errors',["errorLogin"=>"Ocurrio un error al iniciar sesion<br>Intente nuevamente despues de unos minutos."]);
            }
            $userInfo=[
                "id"=>$userInfo[0]["idUsuario"],
                "user"=>$userInfo[0]["usuarioUsuario"]
            ];
            session()->start();
            session()->set("usuario", $userInfo);
            return redirect()->to(base_url().'inicio'); 
        }
    }
}
