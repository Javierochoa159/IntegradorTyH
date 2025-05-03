<?php

namespace App\Validation;

class ValidationAlphaNumSpacePunct{
    public function valid_alphanum_space_punct($string){
        if ($string === null) {
            return false;
        }
        if (!is_string($string)){
            $string = (string) $string;
        }
        if(preg_match('/^(?=(?:.*[A-Za-z]){3,})[ÁÄÉËÍÏÓÖÚÜáäéëíïóöúüñÑA-Za-z0-9 \s,;.:¡!¿?\'"@()<>#=_*\/+-]+$/', $string) !== 1){
            return false;
        }
        return true;
    }
}
