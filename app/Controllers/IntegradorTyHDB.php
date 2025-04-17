<?php

namespace App\Controllers;
use App\Models\IntegradorTyHDBModel;
class IntegradorTyHDB extends BaseController
{
    public function index(){
        if(new IntegradorTyHDBModel())echo "DB creada";else echo "DB no Creada";
    }
}