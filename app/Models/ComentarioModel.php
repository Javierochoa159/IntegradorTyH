<?php
namespace App\Models;
use CodeIgniter\Model;

class ComentarioModel extends Model{
    protected $table = "Comentarios";
    protected $primaryKey = 'idComentario';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['idSubTarea','idUsuario','comentario','estado'];
    protected bool $updateOnlyChanged = true;
}