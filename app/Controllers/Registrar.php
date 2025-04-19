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
        $usuario=new UsuarioModel();
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
            return redirect()->to("/public/index.php/registrar")->withInput();
        }
        $user=new UsuarioModel();
        $mensaje=array();
        $data=$user->where('usuarioUsuario',$post["user"])->findColumn("idUsuario");
        if(!empty($data)){
            $mensaje["user"]= "El usuario ingresado ya está en uso.";
        }
        $data=$user->where('emailUsuario',$post["email"])->findColumn("idUsuario");
        if(!empty($data)){
            $mensaje["email"]= "El correo ingresado ya está en uso.";
        }
        if(!empty($mensaje)){
            return redirect()->to("/public/index.php/registrar")->withInput()->with('errors',$mensaje);
        }else{
            $this->encriptado=\Config\Services::encrypter();
            $userId=$usuario->insert([
                "usuarioUsuario"=>$post["user"],
                "emailUsuario"=>$post["email"],
                "passUsuario"=>base64_encode($this->encriptado->encrypt($post["pass"])),
            ],true);
            $post["pass"]="";
            if(!$userId){
                return redirect()->to("/public/index.php/registrar")->with('errors',["errorRegistrar"=>"Ocurrio un error al crear la cuenta<br>Intente nuevamente despues de unos minutos."]);
            }
            $sesion=session();
            $user=["id"=>$userId,"user"=>$post["user"]];
            $sesion->set("usuario", $user);
            return redirect()->to('public/index.php/login'); 
        }
    }
}
