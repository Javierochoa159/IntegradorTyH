<?php
namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model{
    protected $table = "Usuarios";
    protected $primaryKey = 'idUsuario';
    protected $useAutoIncrement=true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['usuarioUsuario', 'emailUsuario','passUsuario'];
    protected bool $updateOnlyChanged = true;
}
