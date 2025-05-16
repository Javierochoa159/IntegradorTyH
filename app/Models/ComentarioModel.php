<?php
namespace App\Models;
use CodeIgniter\Model;

class ComentarioModel extends Model{
    protected $table = "Comentarios";
    protected $primaryKey = 'idComentario';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['idSubTarea','idUsuario','comentario','estado','deleted_at'];
    protected bool $updateOnlyChanged = true;

    public function insertNewComentario($idSubTarea,$comentarioSubTarea){
        $sqlIn=[
                    "idSubTarea"=>$idSubTarea,
                    "idUsuario"=>session()->get("usuario")["id"],
                    "comentario"=>$comentarioSubTarea
                ];
        return $this->insert($sqlIn);
    }
    public function deleteAllComentariosFromSubTarea($idSubTarea){
        return $this->where("idSubTarea=".$idSubTarea)->delete();
    }
    public function deleteAllComentariosFromTarea($idTarea){
        $db = \Config\Database::connect();
        $sql='  UPDATE comentarios 
                LEFT JOIN subTareas ON subTareas.idSubTarea=comentarios.idSubTarea
                LEFT JOIN tareas ON tareas.idTarea=subTareas.idTarea
                SET comentarios.deleted_at = "'.\CodeIgniter\I18n\Time::now()->format('Y-m-d H:i:s').'"
                WHERE tareas.idTarea='.$idTarea.'
                        AND comentarios.deleted_at IS NULL
                ';
        $res   = $db->query($sql);
        $db->close();
        return $res;
    }
}