<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

Class Login extends BaseController{
    private $encriptado;
    public function __construct(){ helper ('form'); }
    function index(){
        $sesion=session();
        if(!$sesion->has("Usuario")){
            $sesion->destroy();
            return view("LoginView");
        }
        $data=["usuario"=>$sesion->get("Usuario")];
        return view("InicioView",$data);

    }
    public function exito(){
        $this->encriptado=\Config\Services::encrypter();
        $email=$this->request->getPost(['email'])["email"];
        $pass=$this->request->getPost(['pass'])["pass"];
        $user=new UsuarioModel();
        $data=$user->where('emailUsuario',$email)->findColumn("passUsuario");
        $passDB=$this->encriptado->decrypt(base64_decode($data[0]));
        $userInfo=$user->where('emailUsuario',$email)->where("passUsuario",$data[0])->find();
        if($pass===$passDB){
            $sesion=session();
            $userInfo=[
                "idUsuario"=>$userInfo[0]["idUsuario"],
                "usuarioUsuario"=>$userInfo[0]["usuarioUsuario"],
                "emailUsuario"=>$userInfo[0]["emailUsuario"]
            ];
            $sesion->set("Usuario", $userInfo);
            $sesion->setFlashData('mensaje',"Iniciando sesion");
            return redirect()->to('public/index.php/login'); 
        }
    }
}