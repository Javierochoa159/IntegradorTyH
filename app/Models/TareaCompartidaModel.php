<?php
namespace App\Models;
use CodeIgniter\Model;

class TareaModelModel extends Model{
    protected $table = "TareasCompartidas";
    protected $allowedFields = ['tipoTareaCompartida','estadoTareaCompartida'];

    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected bool $updateOnlyChanged = true;
}