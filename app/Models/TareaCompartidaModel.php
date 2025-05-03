<?php
namespace App\Models;
use CodeIgniter\Model;

class TareaCompartidaModel extends Model{
    protected $table = "TareasCompartidas";
    protected $primaryKey = 'idTareaCompartida';
    protected $useAutoIncrement=true;
    protected $allowedFields = ['idTarea','idSubTarea','idUsuario','tipoTareaCompartida','estadoTareaCompartida'];
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected bool $updateOnlyChanged = true;
}