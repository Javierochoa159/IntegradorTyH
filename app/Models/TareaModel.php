<?php
namespace App\Models;
use CodeIgniter\Model;

class TareaModel extends Model{
    protected $table = "Tareas";
    protected $primaryKey = 'idTarea';
    protected $useAutoIncrement=true;
    protected $allowEmptyInserts=true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['tituloTarea', 'descripcionTarea','prioridadTarea','estadoTarea','fechaVencimientoTarea','fechaRecordatorioTarea','colorTarea','autorTarea'];
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
