<?php
namespace App\Models;
use CodeIgniter\Model;

class TpIntegradorDBModel extends Model{
    private $forge;
    public function __construct(){
        $res=1;
        try{
            $this->forge = \Config\Database::forge();
        }catch(\Exception $e1){
            $res=$e1->getCode();
            try{
                $this->forge = \Config\Database::forge('aux');
            }
            catch(\Exception $e2){
                echo $e2->getMessage();
                exit();
            }
        }
        if(!$res){
            $res=$this->forge->createDatabase('TpIntegrador', true);
        }
        if($res){
            $this->forge = \Config\Database::forge();
            $this->initUsuarioModel();
            $this->initTareaModel();
            $this->initSubTareaModel();
            $this->initTareaCompartidaModel();
        }
    }
    private function initUsuarioModel(){
        $fields=[
            "idUsuario" => [
                "type" => "INT",
                "unasigned" => true,
                "auto_increment" => true
            ],
            "usuarioUsuario" => [
                "type" => "varchar",
                "constraint" => 255
            ],
            "emailUsuario" => [
                "type" => "varchar",
                "constraint" => 255
            ],
            "passUsuario" => [
                "type" => "varchar",
                "constraint" => 255,
            ]
        ];

        $this->forge->addKey("idUsuario",true);
        $this->forge->addKey("usuarioUsuario",false,true);
        $this->forge->addKey("emailUsuario",false,true);

        $attributes = [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collate'=> 'utf8mb4_unicode_ci',
        ];
        $this->forge->addField($fields);
        $this->forge->createTable("Usuarios", true, $attributes);
    }
    private function initTareaModel(){
        $fields=[
            "idTarea" => [
                "type" => "INT",
                "unasigned" => true,
                "auto_increment" => true
            ],
            "tituloTarea" => [
                "type" => "varchar",
                "constraint" => 30
            ],
            "descripcionTarea" => [
                "type" => "varchar",
                "constraint" => 255
            ],
            "prioridadTarea" => [
                "type" => "enum",
                "constraint"=> ['1','2','3']
            ],
            "estadoTarea"=>[
                "type" => "enum",
                "constraint" => ["1","2","3"],
                "default" => 1
            ],
            "fechaVencimientoTarea" => [
                "type" => "datetime",
            ],
            "fechaRecordatorioTarea" => [
                "type" => "datetime",
                "null" => true
            ],
            "colorTarea" => [
                "type" => "varchar",
                "constraint" => 15,
                "default"=> "#0000ff"
            ],
            "autorTarea" => [
                "type"=> "int",
                "unasigned" => true
            ]
        ];

        $this->forge->addKey("idTarea",true);
        $this->forge->addForeignKey("autorTarea",
                                "Usuarios",
                                "idUsuario",
                                "cascade",
                                "cascade",
                                "fk_tareas_autorTarea");

        $attributes = [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collate'=> 'utf8mb4_unicode_ci',
        ];
        $this->forge->addField($fields);
        $this->forge->createTable("Tareas", true, $attributes);
    }
    private function initSubTareaModel(){
        $fields=[
            "idSubTarea" => [
                "type" => "INT",
                "unasigned" => true,
                "auto_increment" => true
            ],
            "descripcionSubTarea" => [
                "type" => "varchar",
                "constraint" => 255
            ],
            "estadoSubTarea"=>[
                "type" => "enum",
                "constraint" => ["1","2"],
                "default" => 1
            ],
            "prioridadSubTarea"=>[
                "type" => "enum",
                "constraint" => ["1","2","3"],
                "null" => true
            ],
            "fechaVencimientoSubTarea" => [
                "type" => "datetime",
                "null" => true
            ],
            "comentarioSubTarea" => [
                "type" => "varchar",
                "constraint" => 50,
                "null" => true
            ],
            "responsableSubTarea" => [
                "type"=> "int",
                "unasigned" => true,
                "null" => true
            ],
            "autorSubTarea" => [
                "type"=> "int",
                "unasigned" => true
            ]
        ];

        $this->forge->addKey("idSubTarea",true);
        $this->forge->addForeignKey("responsableSubTarea",
                                "Usuarios",
                                "idUsuario",
                                "cascade",
                                "cascade",
                                "fk_subTareas_responsableSubTarea");
        $this->forge->addForeignKey("autorSubTarea",
                                "Usuarios",
                                "idUsuario",
                                "cascade",
                                "cascade",
                                "fk_subTareas_autorSubTarea");

        $attributes = [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collate'=> 'utf8mb4_unicode_ci',
        ];
        $this->forge->addField($fields);
        $this->forge->createTable("SubTareas", true, $attributes);
    }
    private function initTareaCompartidaModel(){
        $fields=[
            "idTarea" => [
                "type" => "INT"
            ],
            "idUsuario" => [
                "type" => "INT"
            ]
        ];

        $this->forge->addForeignKey("idTarea",
                                "Tareas",
                                "idTarea",
                                "cascade",
                                "cascade",
                                "fk_tareasCompartidas_idTarea");
        $this->forge->addForeignKey("idUsuario",
                                "Usuarios",
                                "idUsuario",
                                "cascade",
                                "cascade",
                                "fk_tareasCompartidas_idUsuario");

        $attributes = [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collate'=> 'utf8mb4_unicode_ci',
        ];
        $this->forge->addField($fields);
        $this->forge->createTable("TareasCompartidas", true, $attributes);
    }
}
