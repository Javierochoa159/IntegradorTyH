<?php
namespace App\Models;
use CodeIgniter\Model;

class SubTareaModel extends Model{
    protected $table = "SubTareas";
    protected $primaryKey = 'idSubTarea';
    protected $useAutoIncrement=true;
    protected $allowEmptyInserts=true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['descripcionSubTarea','estadoSubTarea','fechaVencimientoSubTarea','comentarioSubTarea','responsableSubTarea','autorSubTarea'];
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
