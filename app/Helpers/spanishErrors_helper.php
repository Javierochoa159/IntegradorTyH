<?php

use App\Libraries\spanishErrorsLibrary;

if(!function_exists("spanishErrorMessages")){
    function spanishErrorMessages($rules=array()){
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
        $sem=new SpanishErrorsLibrary();
        foreach($mensajesReglas as $entrada=>$reglas){
            $mensajes[$entrada]=$sem->obtenerMensajes($reglas,$entrada);
        }
        return $mensajes;
    }
}
