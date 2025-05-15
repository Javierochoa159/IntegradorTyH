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

    public function getIdUsuario(){
        return $this->select("idUsuario")->find(session()->get("usuario")["id"]);
    }
    public function getPassUser($email){
        return $this->where('emailUsuario',$email)->findColumn("passUsuario");
    }
    public function getUserInfo($email){
        return $this->where('emailUsuario',$email)->find();
    }
    public function getIdUsuarioAtUserName($userName){
        return $this->where('usuarioUsuario',$userName)->findColumn("idUsuario");
    }
    public function getIdUserAtEmail($email){
        return $this->where('emailUsuario',$email)->findColumn("idUsuario");
    }
    public function insertNewUser($user, $email, $pass){
        return $this->insert([
                "usuarioUsuario"=>$user,
                "emailUsuario"=>$email,
                "passUsuario"=>$pass,
            ],true);
    }

    public function getEmailUser($id){
        return $this->select("emailUsuario")->find($id);
    }

    public function getIdEmailUser($userName){
        return $this->select("idUsuario, emailUsuario")->where('usuarioUsuario',$userName)->find();
    }
}
