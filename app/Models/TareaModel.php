<?php
namespace App\Models;
use CodeIgniter\Model;

class TareaModel extends Model{
    protected $table = "Tareas";
    protected $primaryKey = 'idTarea';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['tituloTarea', 'descripcionTarea','prioridadTarea','estadoTarea','fechaVencimientoTarea','fechaRecordatorioTarea','colorTarea','recordatorioNotificado','tareaArchivada','autorTarea','deleted_at'];
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    public function insertNewTarea($tituloTarea, $descripcionTarea,$prioridadTarea,$colorTarea,$fechaVencimientoTarea,$fechaRecordatorioTarea){
        $sqlIn=[
                "tituloTarea"=>$tituloTarea,
                "descripcionTarea"=>$descripcionTarea,
                "prioridadTarea"=>$prioridadTarea,
                "colorTarea"=>$colorTarea,
                "fechaVencimientoTarea"=>$fechaVencimientoTarea,
                "autorTarea"=> session()->get("usuario")["id"]
            ];
        if($fechaRecordatorioTarea!=null) $sqlIn["fechaRecordatorioTarea"]=$fechaRecordatorioTarea;
        return $this->insert($sqlIn);
    }

    public function getTodasLasTareas($orden){
        $db = \Config\Database::connect();
        $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.prioridadTarea AS prioridad, tareas.prioridadTarea AS prioridadOrdenada, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, tareas.recordatorioNotificado AS recoNotify, tareas.autorTarea AS autor, "tarea" AS tarea_subtarea
                                        FROM tareas
                                        LEFT JOIN tareasCompartidas ON tareasCompartidas.idTarea=tareas.idTarea
                                        WHERE   ((tareas.autorTarea = '.session()->get("usuario")["id"].' AND tareas.tareaArchivada = 0)
                                                OR (tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].' AND tareasCompartidas.estadoTareaCompartida = "1" AND tareasCompartidas.idSubTarea IS NULL))
                                                AND tareas.deleted_at IS NULL
                                        UNION
                                            SELECT subTareas.idSubTarea AS id, subTareas.descripcionSubTarea AS titulo, subTareas.prioridadSubTarea AS prioridad,CASE 
                                                WHEN subTareas.prioridadSubTarea = 4 THEN 3
                                                WHEN subTareas.prioridadSubTarea = 3 THEN 2 
                                                WHEN subtareas.prioridadSubTarea = 2 THEN 1
                                                WHEN subtareas.prioridadSubTarea = 1 THEN 0
                                            END AS prioridadOrdenada, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, NULL AS fechaRecordatorio, subTareas.colorSubTarea AS color, NULL AS recoNotify, subTareas.autorSubTarea AS autor, "subtarea" AS tarea_subtarea
                                            FROM subTareas
                                            LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea=subTareas.idSubTarea
                                            WHERE tareasCompartidas.estadoTareaCompartida="1"
                                                    AND subTareas.deleted_at IS NULL
                                                    AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].' 
                                        ';
        $sql.=$orden;
        $query   = $db->query($sql);
        $tareas = $query->getResultArray();
        $db->close();
        return $tareas;
    }

    public function recordatorioTarea($idTarea, $recordatorioNotificado){
        return $this->update($idTarea,["recordatorioNotificado"=>$recordatorioNotificado]);
    }

    public function todasMisTareasActivas($orden){
        $db = \Config\Database::connect();
        $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.prioridadTarea AS prioridad, tareas.prioridadTarea AS prioridadOrdenada, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, "tarea" AS tarea_subtarea
                                        FROM tareas
                                        WHERE tareas.autorTarea = '.session()->get("usuario")["id"].'
                                                AND tareas.tareaArchivada = 0
                                                AND tareas.deleted_at IS NULL
                                        ';
        $sql.=$orden;
        $query   = $db->query($sql);
        $tareas = $query->getResultArray();
        $db->close();
        return $tareas;
    }

    public function todasLasTareasCompartidas($orden){
        $db = \Config\Database::connect();
        $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.prioridadTarea AS prioridad, tareas.prioridadTarea AS prioridadOrdenada, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, "tarea" AS tarea_subtarea
                                    FROM tareas
                                    LEFT JOIN tareasCompartidas ON tareasCompartidas.idTarea=tareas.idTarea
                                    WHERE tareasCompartidas.estadoTareaCompartida = "1"
                                            AND tareas.deleted_at IS NULL
                                            AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].'
                                            AND tareasCompartidas.idSubTarea IS NULL
                                    UNION
                                        SELECT subTareas.idSubTarea AS id, subTareas.descripcionSubTarea AS titulo, subTareas.prioridadSubTarea AS prioridad,CASE 
                                                WHEN subTareas.prioridadSubTarea = 4 THEN 3
                                                WHEN subTareas.prioridadSubTarea = 3 THEN 2 
                                                WHEN subtareas.prioridadSubTarea = 2 THEN 1
                                                WHEN subtareas.prioridadSubTarea = 1 THEN 0
                                            END AS prioridadOrdenada, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, "" AS fechaRecordatorio, subTareas.colorSubTarea AS color, "subtarea" AS tarea_subtarea
                                        FROM subTareas
                                        LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea=subTareas.idSubTarea
                                        WHERE tareasCompartidas.estadoTareaCompartida = "1"
                                                AND subTareas.deleted_at IS NULL
                                                AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].'
                                    ';
        $sql.=$orden;
        $query   = $db->query($sql);
        $tareas = $query->getResultArray();
        $db->close();
        return $tareas;
    }

    public function todasMisTareas($orden){
        $db = \Config\Database::connect();
        $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.prioridadTarea AS prioridad, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, "tarea" AS tarea_subtarea
                                        FROM tareas
                                        WHERE tareas.autorTarea = '.session()->get("usuario")["id"].'
                                                AND tareas.tareaArchivada = 1
                                                AND tareas.deleted_at IS NULL
                                        ';
        $sql.=$orden;
        $query   = $db->query($sql);
        $tareas = $query->getResultArray();
        $db->close();
        return $tareas;
    }

    public function todasLasSubTareasH($idTarea,$orden){
        $db = \Config\Database::connect();
        $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.descripcionTarea AS descripcion, tareas.prioridadTarea AS prioridad, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, tareas.autorTarea AS autor, NULL AS tipoTC, "tarea" AS tarea_subtarea
                                    FROM tareas
                                    LEFT JOIN tareasCompartidas ON tareasCompartidas.idTarea=tareas.idTarea
                                    WHERE tareas.idTarea = '.$idTarea.'
                                            AND tareas.tareaArchivada = 1
                                            AND tareas.deleted_at IS NULL
                                            AND tareas.autorTarea = '.session()->get("usuario")["id"].'
                                    UNION
                                        SELECT subTareas.idSubTarea AS id, "" AS titulo, subTareas.descripcionSubTarea AS descripcion, subTareas.prioridadSubTarea AS prioridad, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, "" AS fechaRecordatorio, subTareas.colorSubTarea AS color, subTareas.autorSubTarea AS autor, NULL AS tipoTC, "subtarea" AS tarea_subtarea
                                        FROM subTareas
                                        WHERE subTareas.idTarea = '.$idTarea.'
                                              AND subTareas.deleted_at IS NULL
                                    ';
        $sql.=$orden;
        $query   = $db->query($sql);
        $tarea_subTareas = $query->getResultArray();
        $db->close();
        return $tarea_subTareas;
    }

    public function todasLasSubTareas($idTarea,$orden){
        $db = \Config\Database::connect();
        $sql='SELECT tareas.idTarea AS id, tareas.tituloTarea AS titulo, tareas.descripcionTarea AS descripcion, tareas.prioridadTarea AS prioridad, tareas.estadoTarea AS estado, tareas.fechaVencimientoTarea AS fechaVencimiento, tareas.fechaRecordatorioTarea AS fechaRecordatorio, tareas.colorTarea AS color, tareas.autorTarea AS autor, CASE WHEN tareasCompartidas.tipoTareaCompartida IS NOT NULL THEN tareasCompartidas.tipoTareaCompartida ELSE 0 END AS tipoTC, "tarea" AS tarea_subtarea
                                    FROM tareas
                                    LEFT JOIN tareasCompartidas ON tareasCompartidas.idTarea=tareas.idTarea
                                    WHERE tareas.idTarea='.$idTarea.'
                                          AND tareas.deleted_at IS NULL
                                            AND (
                                                (tareas.autorTarea = '.session()->get("usuario")["id"].' AND tareas.tareaArchivada = 0)
                                                OR
                                                (tareasCompartidas.estadoTareaCompartida="1" AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].')
                                                )
                                    UNION
                                        SELECT subTareas.idSubTarea AS id, "" AS titulo, subTareas.descripcionSubTarea AS descripcion, subTareas.prioridadSubTarea AS prioridad, subTareas.estadoSubTarea AS estado, subTareas.fechaVencimientoSubTarea AS fechaVencimiento, "" AS fechaRecordatorio, subTareas.colorSubTarea AS color, subTareas.autorSubTarea AS autor, "" AS tipoTC, "subtarea" AS tarea_subtarea
                                        FROM subTareas
                                        LEFT JOIN tareasCompartidas ON tareasCompartidas.idSubTarea=subTareas.idSubTarea
                                        WHERE subTareas.idTarea = '.$idTarea.'
                                              AND subTareas.deleted_at IS NULL
                                                AND (subTareas.autorSubTarea = '.session()->get("usuario")["id"].'
                                                    OR
                                                    (tareasCompartidas.estadoTareaCompartida="1" AND tareasCompartidas.idUsuario = '.session()->get("usuario")["id"].')
                                                    )
                                    ';
        $sql.=$orden;
        $query   = $db->query($sql);
        $tarea_subTareas = $query->getResultArray();
        $db->close();
        return $tarea_subTareas;
    }

    public function updateTarea($idTarea,$tituloTarea,$descripcionTarea,$prioridadTarea,$colorTarea,$fechaRecordatorioTarea){
        $sqlIn=[
                    "tituloTarea"=>$tituloTarea,
                    "descripcionTarea"=>$descripcionTarea,
                    "prioridadTarea"=>$prioridadTarea,
                    "colorTarea"=>$colorTarea
                ];
        if($fechaRecordatorioTarea!=null) $sqlIn["fechaRecordatorioTarea"]=$fechaRecordatorioTarea;
        return $this->update($idTarea,$sqlIn);
    }

    public function getAutorTarea($idTarea){
        return $this->select("autorTarea")->find($idTarea);
    }

    public function getEstadoTarea($idTarea){
        return $this->select("estadoTarea")->find($idTarea);
    }
    public function updateEstadoTarea($idTarea,$estado){
        return $this->update($idTarea,["estadoTarea"=>$estado]);
    }

    public function isValidFinalizarTarea($idTarea){
        $db = \Config\Database::connect();
        $sql='  SELECT COALESCE(SUM(CASE WHEN estadoSubTarea = "3" THEN 1 ELSE 0 END), 0) AS totalFinalizadas,
                        COUNT(estadoSubTarea) AS totalSubTareas
                        
                FROM subTareas
                WHERE idTarea='.$idTarea.'
                ';
        $query   = $db->query($sql);
        $datos = $query->getResultArray();
        $db->close();
        return $datos;
    }

    public function archivarTarea($idTarea){
        return $this->update($idTarea,["tareaArchivada"=>true]);
    }

    public function getDescripcionTarea($idTarea){
        return $this->select("descripcionTarea")->find($idTarea);
    }

    public function anexarTarea($idTarea,$viejaDesc,$anexDesc){
        return $this->update($idTarea,["descripcionTarea"=>$viejaDesc."\n".$anexDesc]);
    }

    public function getIdTareaSubTarea($idSubTarea){
        return $this->select("tareas.idTarea")->join("subTareas","subTareas.idTarea=tareas.idTarea")->where("subTareas.idSubTarea=".$idSubTarea)->find();
    }

    public function deleteTarea($idTarea){
        return $this->delete($idTarea);
    }
}
