<?php

namespace App\Validation;

class ValidationPass{
    public function valid_pass($pass){
        if ($pass === null) {
            return false;
        }
        if (!is_string($pass)) {
            $pass = (string) $pass;
        }
        if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@|$%&¡!¿?_-])[A-Za-z\d#@|$%&¡!¿?_-]+$/', $pass) !== 1){
            return false;
        }
        return true;
    }
}
