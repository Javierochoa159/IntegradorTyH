<?php namespace App\Libraries;

class SpanishErrorsLibrary {

    public function obtenerMensajes($reglas,$entrada){
        $mensajesReglas=array();
        for($i= 0; $i<sizeof($reglas);$i++){
            switch($this->arregloString($reglas[$i])){
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
                case "valid_pass": $mensajesReglas["valid_pass"] = "Contraseña invalida<br>Tiene que contener letras y numeros sin espacios<br>Al menos una Mayuscula<br>Al menos un caracter especial #@|$%&¡!¿?_-"; break;
                case "valid_alphanum": $mensajesReglas["valid_alphanum"] = "Solo puede contener letras sin espacios<br>Minimo 3 letras<br>Puede tener numeros"; break;
                case "valid_alphanum_dash": $mensajesReglas["valid_alphanum_dash"] = "Solo puede contener letras sin espacios<br>Minimo 3 letras<br>Puede tener numeros<br>Puede tener _ y/o -"; break;
                case "valid_alphanum_space": $mensajesReglas["valid_alphanum_space"] = "Solo puede contener letras<br>Minimo 3 letras<br>Puede tener numeros<br>Puede tener espacios"; break;
            }
        }
        return $mensajesReglas;
    }
    private function arregloString($regla){
        if(is_array($regla)) return $regla[0];
        else return $regla;
    }

}