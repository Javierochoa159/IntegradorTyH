<?php

namespace App\Controllers;
use App\Models\TpIntegradorDBModel;
class TpIntegradorDB extends BaseController
{
    public function index(){
        $tpIDB=new TpIntegradorDBModel();
    }
}