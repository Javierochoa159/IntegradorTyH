<?php

namespace App\Controllers;

use App\Models\TareaModel;

class Tareas extends BaseController{
    function index(){
        return view("TareasView");
    }
}