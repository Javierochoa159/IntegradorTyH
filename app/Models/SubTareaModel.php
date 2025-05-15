<?php
namespace App\Models;
use CodeIgniter\Model;

class SubTareaModel extends Model{
    protected $table = "SubTareas";
    protected $primaryKey = 'idSubTarea';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['descripcionSubTarea','estadoSubTarea','prioridadSubTarea','fechaVencimientoSubTarea','colorSubTarea','responsableSubTarea','autorSubTarea','idTarea'];
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    public function insertNewSubTarea($descripcionSubTarea,$prioridadSubTarea,$colorSubTarea,$fechaVencimientoSubTarea,$idTarea){
        $sqlIn=[
                    "descripcionSubTarea"=>$descripcionSubTarea,
                    "prioridadSubTarea"=>$prioridadSubTarea,
                    "colorSubTarea"=>$colorSubTarea,
                    "autorSubTarea"=> session()->get("usuario")["id"],
                    "responsableSubTarea"=>session()->get("usuario")["id"],
                    "idTarea"=>$idTarea
                ];
        if($fechaVencimientoSubTarea!=null){
            $sqlIn["fechaVencimientoSubTarea"]=$fechaVencimientoSubTarea;
        }
        return $this->insert($sqlIn,true);
    }

    public function todosLosComentarios($idSubTarea){
        $db = \Config\Database::connect();
        $sql='SELECT subTareas.idSubTarea AS id, subTareas.descripcionSubTarea AS descripcion, subTareas.prioridadSubTarea AS prioridad, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, subTareas.colorSubTarea AS color, subTareas.autorSubTarea AS autor, CASE WHEN tareasCompartidas.tipoTareaCompartida IS NOT NULL THEN tareasCompartidas.tipoTareaCompartida ELSE NULL END AS tipoTC, "subtarea" AS subtarea_comentario
                                    FROM subTareas
                                    LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea=subTareas.idSubTarea
                                    WHERE subTareas.idSubTarea = '.$idSubTarea.'
                                            AND (subTareas.autorSubTarea = '.session()->get("usuario")["id"].'
                                                    OR
                                                    (tareasCompartidas.estadoTareaCompartida="1" 
                                                    AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].')
                                                )
                                    UNION
                                        SELECT comentarios.idComentario AS id, comentarios.comentario AS descripcion, "" AS prioridad, "" AS estado, "" AS fechaVencimiento, "" AS color, comentarios.idUsuario AS autor, NULL AS tipoTC, "comentario" AS subtarea_comentario
                                        FROM comentarios
                                        LEFT JOIN subTareas ON subTareas.idSubTarea= comentarios.idSubTarea
                                        LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea= comentarios.idSubTarea
                                        WHERE   comentarios.estado = 1
                                                AND comentarios.idSubTarea='.$idSubTarea.'
                                                AND (subTareas.autorSubTarea = '.session()->get("usuario")["id"].' 
                                                        OR
                                                        (tareasCompartidas.estadoTareaCompartida="1" 
                                                        AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].')
                                                    )
                                    ORDER BY id DESC
                                    ';
        $query   = $db->query($sql);
        $subTarea_comentarios = $query->getResultArray();
        $db->close();
        return $subTarea_comentarios;
    }

    public function updateSubTarea($idSubTarea,$descripcionSubTarea,$prioridadSubTarea,$colorSubTarea){
        $sqlIn=[
                    "descripcionSubTarea"=>$descripcionSubTarea,
                    "prioridadSubTarea"=>$prioridadSubTarea,
                    "colorSubTarea"=>$colorSubTarea
                ];
        return $this->update($idSubTarea,$sqlIn);
    }

    public function getDescripcionSubTarea($idSubTarea){
        return $this->select("descripcionSubTarea")->find($idSubTarea);
    }

    public function anexarSubTarea($idSubTarea,$viejaDesc,$anexDesc){
        return $this->update($idSubTarea,["descripcionTarea"=>$viejaDesc."\n".$anexDesc]);
    }

    public function getEstadoSubTarea($idSubTarea){
        return $this->select("estadoSubTarea")->find($idSubTarea);
    }
    public function updateEstadoSubTarea($idSubTarea,$estado){
        return $this->update($idSubTarea,["estadoSubTarea"=>$estado]);
    }

    public function getAutorSubTarea($idSubTarea){
        return $this->select("autorSubTarea")->find($idSubTarea);
    }

    public function updateResponsableSubTarea($idSubTarea,$idUsuario){
        return $this->update($idSubTarea,["responsableSubTarea"=>$idUsuario]);
    }

    public function deleteSubTarea($idSubTarea){
        return $this->delete($idSubTarea);
    }

    public function deleteSubTareasTarea($idTarea){
        return $this->where("idTarea=".$idTarea)->delete();
    }

}
