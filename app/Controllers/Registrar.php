<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Encryption\Encryption;

Class Registrar extends BaseController{
    
    private $encriptado;
    public function __construct(){ 
        helper ('form');
        $this->encriptado=\Config\Services::encrypter();
    }
    function index(){
        return view("RegistrarView");
    }
    public function exito(){
        $usuario=new UsuarioModel();
        $post=$this->request->getPost(['user',"email","pass"]);
        $validacion = service('validation');
        $reglas=[
            "user" => "required|min_length[4]|max_length[15]|alpha_numeric",
            "email" => "required|min_length[5]|max_length[255]",
            "pass" => "required|min_length[8]|max_length[16]|alpha_numeric|in_list[#|_\$%&¡!¿?]"
        ];
        $validacion->setRules($reglas,errorMessages($reglas));
        if (!$validacion->withRequest($this->request)->run())
        {
            return redirect()->to("/public/index.php/registrar")->withInput()->with('errors',$validacion->getErrors());
        }
        exit();


        $user=new UsuarioModel();
        $mensaje=array();
        $data=$user->where('usuarioUsuario',$post["user"])->findColumn("idUsuario");
        if(!empty($data)){
            $mensaje["usuario"]= "El usuario ingresado ya está en uso.";
        }
        $data=$user->where('emailUsuario',$post["email"])->findColumn("idUsuario");
        if(!empty($data)){
            $mensaje["email"]= "El correo ingresado ya está en uso.";
        }
        if(!empty($mensaje)){
            return redirect()->to("public/index.php/registrar");
        }elseif(empty($data)){
            $userId=$usuario->insert([
                "usuarioUsuario"=>$post["user"],
                "emailUsuario"=>$post["email"],
                "passUsuario"=>base64_encode($this->encriptado->encrypt($post["pass"])),
            ],true);
            $sesion=session();
            $user=["id"=>$userId,"name"=>$post["user"]];
            $sesion->set("Usuario", $user);
            $sesion->setFlashData('mensaje',"Iniciando sesion");
            return redirect()->to('public/index.php/login'); 
        }
    }
}

function errorMessages($rules=array()){
    $mensajesReglas=array();
    foreach($rules as $entrada=>$reglas){
        $mensajesReglas[$entrada]=array();
        if(is_array($reglas)){
            foreach($reglas as $key=>$value){
                break;
            }
        }
        else{
            $reglas=explode('|',$reglas);
            for($i=0; $i<sizeof($reglas);$i++){
                if(str_contains($reglas[$i],"[")){
                    if(str_contains($reglas[$i],"]")){
                        $reglas[$i]=str_replace("]","",explode("[",$reglas[$i]));
                    }else{
                        $reglas[$i]=explode("[",$reglas[$i]);
                        $reglas[$i][1].="|".str_replace("]","",$reglas[$i+1]);
                    }
                }else{
                    if(str_contains($reglas[$i],"]")){
                        continue;
                    }
                }
                array_push($mensajesReglas[$entrada],$reglas[$i]);
            }
        }
    }
    foreach($mensajesReglas as $entrada=>$reglas){
        $mensajes[$entrada]=obtenerMensajes($reglas,$entrada);
    }
    return $mensajes;
}

function obtenerMensajes($reglas,$entrada){
    $mensajesReglas=array();
    for($i= 0; $i<sizeof($reglas);$i++){
        switch(arregloString($reglas[$i])){
            case "required": $mensajesReglas["required"] = $entrada." no opcional"; break;
            case "min_length":$mensajesReglas["min_length"] = "minimo ".trim($reglas[$i][1],"]")." caracteres"; break;
            case "max_length": $mensajesReglas["max_length"] = "maximo ".trim($reglas[$i][1],"]")." caracteres"; break;
            case "in_list": $mensajesReglas["in_list"] = $entrada." debe contener minimo un caracter especial ".$reglas[$i][1]; break;
            case "not_in_list": $mensajesReglas["not_in_list"] = $entrada." no debe contener ninguno de estos caracter: ".$reglas[$i][1]; break;
            case "numeric": $mensajesReglas["numeric"] = $entrada." solo debe contener numeros"; break;
            case "numeric_space": $mensajesReglas["numeric_space"] = $entrada." solo debe contener numeros<br>Puede tener espacios"; break;
            case "numeric_punct": $mensajesReglas["numeric_punct"] = $entrada." solo debe contener numeros<br>Puede contener ~!#$%&*-_+=|.:"; break;
            case "numeric_punct_space": $mensajesReglas["numeric_punct_space"] = $entrada." solo debe contener numeros<br>Puede tener espacios<br>Puede contener ~!#$%&*-_+=|.:"; break;
            case "numeric_dash": $mensajesReglas["numeric_dash"] = $entrada." solo debe contener numeros<br>Puede contener -_"; break;
            case "numeric_dash_space": $mensajesReglas["numeric_dash_space"] = $entrada." solo debe contener numeros<br>Puede tener espacios<br>Puede contener -_"; break;
            case "alpha": $mensajesReglas["alpha"] = $entrada." solo debe contener letras"; break;
            case "alpha_space": $mensajesReglas["alpha_space"] = $entrada." solo debe contener letras<br>Puede tener espacios"; break;
            case "alpha_punct": $mensajesReglas["alpha_punct"] = $entrada." solo debe contener letras<br>Puede contener ~!#$%&*-_+=|.:"; break;
            case "alpha_punct_space": $mensajesReglas["alpha_punct_space"] = $entrada." solo debe contener letras<br>Puede tener espacios<br>Puede contener ~!#$%&*-_+=|.:"; break;
            case "alpha_dash": $mensajesReglas["alpha_dash"] = $entrada." solo debe contener letras<br>Puede contener -_"; break;
            case "alpha_dash_space": $mensajesReglas["alpha_dash_space"] = $entrada." solo debe contener letras<br>Puede tener espacios<br>Puede contener -_"; break;
            case "alpha_numeric": $mensajesReglas["alpha_numeric"] = $entrada." solo debe contener letras<br>Puede contener numeros"; break;
            case "alpha_numeric_space": $mensajesReglas["alpha_numeric_space"] = $entrada." solo debe contener letras<br>Puede contener numeros<br>Puede tener espacios"; break;
            case "alpha_numeric_punct": $mensajesReglas["alpha_numeric_punct"] = $entrada." solo debe contener letras<br>Puede contener numeros<br>Puede contener ~!#$%&*-_+=|.:"; break;
            case "alpha_numeric_punct_space": $mensajesReglas["alpha_numeric_punct_space"] = $entrada." solo debe contener letras<br>Puede contener numeros<br>Puede tener espacios<br>Puede contener ~!#$%&*-_+=|.:"; break;
            case "exact_length": $mensajesReglas["exact_length"] = $entrada." tiene que tener ".$reglas[$i][1]." caracteres"; break;
            case "integer": $mensajesReglas["integer"] = $entrada." solo puede tener enteros"; break;
            case "is_natural": $mensajesReglas["is_natural"] = $entrada." solo puede tener enteros positivos<br>Puede tener 0"; break;
            case "is_natural_no_zero": $mensajesReglas["is_natural_no_zero"] = $entrada." solo puede tener enteros positivos"; break;
            case "less_than": $mensajesReglas["less_than"] = $entrada." tiene que ser menor a ".$reglas[$i][1]; break;
            case "less_than_equal_to": $mensajesReglas["less_than_equal_to"] = $entrada." tiene que ser menor o igual a ".$reglas[$i][1]; break;
            case "greater_than": $mensajesReglas["greater_than"] = $entrada." tiene que ser mayor a ".$reglas[$i][1]; break;
            case "greater_than_equal_to": $mensajesReglas["greater_than_equal_to"] = $entrada." tiene que ser mayor o igual a ".$reglas[$i][1]; break;
            case "differs": $mensajesReglas["differs"] = $entrada." no debe coincidir con ".$reglas[$i][1]; break;
            case "matches": $mensajesReglas["matches"] = $entrada." no coincide con ".$reglas[$i][1]; break;
            case "valid_email": $mensajesReglas["valid_email"] = "Ingrese un email valido"; break;
            case "valid_emails": $mensajesReglas["valid_emails"] = $entrada." contiene al menos un email no valido"; break;
            case "valid_url": $mensajesReglas["valid_url"] = "Ingrese una URL valida"; break;
        }
    }
    return $mensajesReglas;
}
function arregloString($regla){
    if(is_array($regla)) return $regla[0];
    else return $regla;
}