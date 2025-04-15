<?php

namespace App\Controllers;

//use App\Models\TpIntegradorDB;

class Tareas extends BaseController{
    function index(){
        return view("TareasView");
    }
}