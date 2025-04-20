<?php
namespace App\Models;
use CodeIgniter\Model;

class IntegradorTyHDBModel extends Model{
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
            $res=$this->forge->createDatabase('IntegradorTyH', true);
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

        $this->forge->addPrimaryKey("idUsuario");
        $this->forge->addUniqueKey("usuarioUsuario");
        $this->forge->addUniqueKey("emailUsuario");

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
                "type" => "enum",
                "constraint" => ["#6f3c1ed6","#782069d6","#401664d6","#280555d6","#276d34d6","#035f78d6"],
                "default"=> "#6f3c1ed6"
            ],
            "autorTarea" => [
                "type"=> "int",
                "unasigned" => true
            ]
        ];

        $this->forge->addPrimaryKey("idTarea");
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
                "type" => "int",
                "unasigned" => true,
                "auto_increment" => true
            ],
            "descripcionSubTarea" => [
                "type" => "varchar",
                "constraint" => 255
            ],
            "estadoSubTarea"=>[
                "type" => "enum",
                "constraint" => ["1","2","3"],
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
            ],
            "idTarea" => [
                "type"=> "int",
                "unasigned" => true
            ]
        ];

        $this->forge->addPrimaryKey("idSubTarea");
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
        $this->forge->addForeignKey("idTarea",
                                    "Tareas",
                                    "idTarea",
                                    "cascade",
                                    "cascade",
                                    "fk_subTareas_idTarea");
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
            ],
            "tipoTareaCompartida" => [
                "type" => "enum",
                "constraint" => ["1","2","3"],
                "default"=> 1
            ],
            "estadoTareaCompartida" => [
                "type" => "boolean",
                "default" => true
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
