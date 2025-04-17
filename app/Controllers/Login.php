<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

Class Login extends BaseController{
    private $encriptado;
    public function __construct(){ 
        helper ('form');
    }
    function index(){
        $sesion=session();
        if(!$sesion->has("usuario")){
            $sesion->destroy();
            return view("LoginView");
        }
        $data=["usuario"=>$sesion->get("usuario")];
        return view("InicioView",$data);

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
            return redirect()->to("/public/index.php/login")->withInput();
        }
        
        $user=new UsuarioModel();
        $mensaje=array();
        $data=$user->where('emailUsuario',$post["email"])->findColumn("passUsuario");
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
            return redirect()->to("/public/index.php/login")->withInput()->with('errors',$mensaje);
        }else{
            $userInfo=$user->where('emailUsuario',$post["email"])->find();
            if(empty($userInfo)){
                return redirect()->to("/public/index.php/login")->with('errors',["errorLogin"=>"Ocurrio un error al iniciar sesion<br>Intente nuevamente despues de unos minutos."]);
            }
            $userInfo=[
                "id"=>$userInfo[0]["idUsuario"],
                "user"=>$userInfo[0]["usuarioUsuario"]
            ];
            $sesion=session();
            $sesion->set("usuario", $userInfo);
            return redirect()->to('public/index.php/login'); 
        }
    }
}
