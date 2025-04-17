<?php

namespace App\Validation;

class ValidationAlphaNum{
    public function valid_alphanum($string){
        if ($string === null) {
            return false;
        }
        if (!is_string($string)){
            $string = (string) $string;
        }
        if(preg_match('/^(?=(?:.*[A-Za-z]){3,})[A-Za-z0-9]+$/', $string) !== 1){
            return false;
        }
        return true;
    }
}
