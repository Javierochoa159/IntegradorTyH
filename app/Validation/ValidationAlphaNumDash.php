<?php

namespace App\Validation;

class ValidationAlphaNumDash{
    public function valid_alphanum_dash($string){
        if ($string === null) {
            return false;
        }
        if (!is_string($string)){
            $string = (string) $string;
        }
        if(preg_match('/^(?=(?:.*[A-Za-z]){3,})[A-Za-z0-9_-]+$/', $string) !== 1){
            return false;
        }
        return true;
    }
}