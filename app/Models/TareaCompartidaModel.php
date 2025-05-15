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

    public function insertNewTCSubTarea($idUser,$tipoTC,$idTarea,$idSubTarea){
         $sqlIn=[
                "idUsuario" =>$idUser,
                "tipoTareaCompartida"=>$tipoTC,
                "idTarea"=>$idTarea,
                "idSubTarea"=>$idSubTarea
            ];
        return $this->insert($sqlIn,true);
    }
    public function insertNewTCTarea($idUser,$tipoTC,$idTarea){
         $sqlIn=[
                "idUsuario" =>$idUser,
                "tipoTareaCompartida"=>$tipoTC,
                "idTarea"=>$idTarea
            ];
        return $this->insert($sqlIn,true);
    }

    public function getTCSubTarea($idTarea,$idUser,$idSubTarea){
        return $this->select("idTareaCompartida, tipoTareaCompartida, estadoTareaCompartida")->where("idTarea=".$idTarea." AND idUsuario=".$idUser." AND idSubTarea=".$idSubTarea)->find();
    }

    public function getTCTarea($idTarea,$idUsuario){
        return $this->select("tipoTareaCompartida, estadoTareaCompartida")->where("idTarea=".$idTarea." AND idUsuario=".$idUsuario)->find();
    }

    public function finalizarTC($idTarea){
        return $this->where("idTarea=".$idTarea." AND estadoTareaCompartida='1'")->update(null,["estadoTareaCompartida"=>'3']);
    }

    public function updateTCTarea($idTarea,$idUsuario,$tipoTC){
        return $this->where("idTarea=".$idTarea." AND idUsuario=".$idUsuario)->update(null,["tipoTareaCompartida"=>$tipoTC]);
    }

    public function getEstadoTC($idTC){
        return $this->select("estadoTareaCompartida")->find($idTC);
    }

    public function updateEstadoTC($idTC, $estado){
        return $this->update($idTC,["estadoTareaCompartida"=>$estado]);
    }

    public function finalizarTCSubTarea($idSubTarea){
        return $this->where("idSubTarea=".$idSubTarea." AND estadoTareaCompartida='1'")->update(null,["estadoTareaCompartida"=>'3']);
    }

    public function updateTCSubTarea($idTCSubTarea,$tipoTC){
        return $this->update($idTCSubTarea,["tipoTareaCompartida"=>$tipoTC]);
    }

    public function getIdSubTareaIdUserTC($idTC){
        return $this->select("idSubTarea, idUsuario")->find($idTC);
    }

    public function deleteTC($idTC){
        return $this->delete($idTC);
    }

    public function deleteTCsTarea($idTarea){
        return $this->where("idTarea=".$idTarea)->delete();
    }
    public function deleteTCsSubTarea($idSubTarea){
        return $this->where("idSubTarea=".$idSubTarea)->delete();
    }
}