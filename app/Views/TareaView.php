<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= substr(base_url(),0,-17) ?>Plantilla/Css/tarea.css">
    <title>Tarea</title>
</head>
<body class="dark">
    <header class="col-12 mb-3 py-4 border-bottom border-dark-subtle d-flex justify-content-between">
        <div class="col-3 ps-3">
            <a href="<?= base_url()."inicio"?>">
            <img src="" alt="logo">
        </a>
    </div>
    <div class="col-3 pe-3 d-flex justify-content-end">
            <div id="dark_light" class="btn btn-primary"><img src="https://img.icons8.com/?size=100&id=8gmhfnYGKE8G&format=png&color=000000" alt="dark" onclick="dark_light(event)"></div>
            <a href="<?= base_url()."inicio/logout"?>" class="dark">Salir</a>
        </div>
    </header>
    <section class="col-12 d-flex">
        <aside class="d-flex flex-column justify-content-start">
            <a href="<?= base_url()."inicio"?>" class="col-12 text-decoration-none text-reset mb-3 bg-azulitoNo rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="dark py-2 m-0">Tareas</h5>
            </a>
            <a href="<?= base_url()."inicio/mis_tareas"?>" class="col-12 text-decoration-none text-reset mb-3 dark bg-azulitoNo rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="dark py-2 m-0">Mis tareas</h5>
            </a>
            <a href="<?= base_url()."inicio/tareas_compartidas"?>" class="col-12 text-decoration-none text-reset mb-3 dark bg-azulitoNo rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="dark py-2 m-0">Tareas compartidas</h5>
            </a>
            <a href="<?= base_url()."inicio/historial"?>" class="col-12 text-decoration-none text-reset mb-3 dark bg-azulitoNo rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="dark py-2 m-0">Historial</h5>
            </a>
        </aside>
        <div class="principalDiv">
            <div class="col-12 d-flex flex-column align-items-center justify-content-center">
                <div class="col-12 mb-3 text-start "<?php $mensajeTarea=session()->getFlashdata("mensajeTarea");?>>
                    <h5 class="dark m-0 ms-4 <?php if(isset($mensajeTarea["success"]))echo "border-bottom border-success";elseif(isset($mensajeTarea["error"])) echo "border-bottom border-danger";?>"><?php if(isset($mensajeTarea["mensaje"])) echo $mensajeTarea["mensaje"];?></h5>
                </div>
                <div class="col-11 d-flex justify-content-center">
                    <?php $fechaTarea;if(isset($tarea_subTareas))foreach($tarea_subTareas as $tareaOsubtarea) {
                        if($tareaOsubtarea["tarea_subtarea"]=="tarea"){
                            $visibilidadTarea["autor"]=$tareaOsubtarea["autor"];
                            $visibilidadTarea["estado"]=$tareaOsubtarea["estado"];
                            $visibilidadTarea["tipoTC"]=$tareaOsubtarea["tipoTC"];
                            
                        ?>
                    <div class="tarea col-10 prioriTarea_<?= $tareaOsubtarea["prioridad"]?>" style="background-color:<?= $tareaOsubtarea["color"]?>;">
                        <div class="tituloTarea p-2 mx-2"><h3 class="dark m-0"><?= $tareaOsubtarea["titulo"]?></h3></div>
                        <div class="descripcionTarea p-2 mx-2"><p class="dark"><?= $tareaOsubtarea["descripcion"]?></p></div>
                        <div class="pieTarea">
                            <p class="dark frt"><?php if($tareaOsubtarea["fechaRecordatorio"]!=null) echo substr($tareaOsubtarea["fechaRecordatorio"],0,-3)?></p>
                            <p class="dark fvt"><?= substr($tareaOsubtarea["fechaVencimiento"],0,-3)?><?php $fechaTarea=$tareaOsubtarea["fechaVencimiento"]?></p>
                            <p class="dark et"><?php
                                switch($tareaOsubtarea["estado"]){
                                    case 1: echo "Definida";break;
                                    case 2: echo "En proceso";break;
                                    case 3: echo "Finalizada";break;
                                }
                            ?></p>
                        </div>
                        <div class="dropdown optTarea dark">
                            <button class="btn dropdown-toggle dark" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="https://img.icons8.com/?size=100&id=7m1CoJ6JRUqG&format=png&color=000000" alt="options"></button>
                            <ul class="dropdown-menu dark">
                                <?php if($tareaOsubtarea["tipoTC"]>=2 || $tareaOsubtarea["autor"]==session()->get("usuario")["id"]){?>
                                <li>
                                    <p id="botonModTarea" class="dropdown-item text-reset text-decoration-none mb-0" data-bs-toggle="modal" data-bs-target="#modTareaModal">Modificar</p>
                                </li>
                                <?php }?>
                                <?php if($tareaOsubtarea["estado"]!=3 && $tareaOsubtarea["autor"]==session()->get("usuario")["id"]){?>
                                <li>
                                    <p id="botonCompartirTarea" class="dropdown-item text-reset text-decoration-none mb-0" data-bs-toggle="modal" data-bs-target="#compartirTareaModal">Compartir</p>
                                </li>
                                <?php }?>
                                <?php if($tareaOsubtarea["estado"]!=3){?>
                                <li>
                                    <a class="dropdown-item text-reset text-decoration-none" href="<?php  echo base_url()."tarea/estadotarea/".$idTarea;?>"><?php if($tareaOsubtarea["estado"] == "1") echo "Empezar";elseif($tareaOsubtarea["estado"] == "2") echo "Finalizar";?></a>
                                </li>
                                <?php }elseif($tareaOsubtarea["estado"]==3 && $tareaOsubtarea["autor"]==session()->get("usuario")["id"] && $urlTarea=="tarea"){?>
                                <li>
                                    <a class="dropdown-item text-reset text-decoration-none" href="<?php  echo base_url()."tarea/archivartarea/".$idTarea;?>">Archivar</a>
                                </li>
                               <?php }?>
                               <?php if($tareaOsubtarea["autor"]==session()->get("usuario")["id"]){?>
                                <li>
                                    <p id="botonEliminarTarea" class="dropdown-item text-reset text-decoration-none mb-0" data-bs-toggle="modal" data-bs-target="#eliminarTareaModal">Eliminar</p>
                                </li>
                                <?php }?>
                            </ul>
                        </div>
                        
                    </div>
                    <?php if($tareaOsubtarea["autor"]==session()->get("usuario")["id"]){?>
                        <div class="modal fade" id="eliminarTareaModal" tabindex="-1" aria-labelledby="eliminarTareaModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content dark">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="eliminarTareaModalLabel">Eliminar Tarea</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="deleteTarea" action="<?= base_url().$urlTarea."/eliminartarea"?>" method="post">
                                           <div class="mb-3">
                                                <h3>Seguro que desea eliminar la tarea?</h3>
                                            </div>
                                            <input type="number" name="idTarea" value="<?= $idTarea?>" hidden>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="reset" form="deleteTarea" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" form="deleteTarea" class="btn btn-primary">Eliminar</button>
                                </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                    <div class="modal fade" id="modTareaModal" tabindex="-1" aria-labelledby="modTareaModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content dark">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modTareaModalLabel">Modificar Tarea</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="modTarea" action="<?php if($tareaOsubtarea["autor"]==session()->get("usuario")["id"] || $tareaOsubtarea["tipoTC"]==3) echo base_url($urlTarea."/modtarea/".$idTarea);elseif($tareaOsubtarea["autor"]!=session()->get("usuario")["id"] || $tareaOsubtarea["tipoTC"]==2) echo base_url("tarea/anextarea/".$idTarea);?>" method="post">
                                    <?php if($tareaOsubtarea["autor"]==session()->get("usuario")["id"] || $tareaOsubtarea["tipoTC"]==3){?>
                                        <div class="mb-3">
                                            <label for="tituloTarea" class="col-form-label">Titulo</label>
                                            <input type="text" class="form-control" name="tituloTarea" id="tituloTarea" <?php if(old("tituloTarea")!=null) echo "value='".old("tituloTarea")."'";else echo "value='".$tareaOsubtarea["titulo"]."'"?>>
                                            <p class="mb-0">
                                                <?php if(isset(validation_errors()["tituloTarea"])){
                                                        echo str_replace("tituloTarea","Titulo",validation_errors()["tituloTarea"]);
                                                }?>
                                            </p>
                                        </div>
                                        <div class="mb-3">
                                            <label for="descripcionTarea" class="col-form-label">Descripcion</label>
                                            <textarea class="form-control" name="descripcionTarea" id="descripcionTarea"><?php if(old("descripcionTarea")!=null) echo old("descripcionTarea");else echo $tareaOsubtarea["descripcion"]?></textarea>
                                            <p class="mb-0">
                                                <?php if(isset(validation_errors()["descripcionTarea"])){
                                                        echo str_replace("descripcionTarea","Descripcion",validation_errors()["descripcionTarea"]);
                                                }?>
                                            </p>
                                        </div>
                                        <div class="mb-3 d-flex justify-content-around">
                                            <div class="col-5 d-flex">
                                                <label for="prioridadTarea" class="col-form-label">Prioridad </label>
                                                <select class="ms-2" name="prioridadTarea" id="prioridadTarea" <?php if(old("prioridadTarea")!=null) echo "value='".old("prioridadTarea")."'";else echo "value='".$tareaOsubtarea["prioridad"]."'"?>>
                                                    <option value="1">Baja</option>
                                                    <option value="2">Normal</option>
                                                    <option value="3">Alta</option>
                                                </select>
                                            </div>
                                            <?php $colores=["#6f3c1e5c","#7820695c","#4016645c","#2805555c","#276d345c","#035f785c"]?>
                                            <div class="col-5 d-flex">
                                                <label for="colorTarea" class="col-form-label">Color </label>
                                                <div class="dropdown colorTarea">
                                                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><div style="background-color:<?= $tareaOsubtarea["color"]?>;"></div></button>
                                                    <ul class="dropdown-menu dark">
                                                        <li class="labelColorF" onClick="clickLabelColorTarea(event)">
                                                            <label for="colorTarea1" class="dropdown-item" data-value="<?= $colores[0]?>" style="background-color: <?= $colores[0]?>"></label>
                                                            <input type="radio" name="colorTarea" id="colorTarea1" value="<?= $colores[0]?>" <?php if($tareaOsubtarea["color"]==$colores[0])echo "checked";?> hidden onChange="checkColorTarea(event)">
                                                        </li>
                                                        <li onClick="clickLabelColorTarea(event)">
                                                            <label for="colorTarea2" class="dropdown-item" data-value="<?= $colores[1]?>" style="background-color: <?= $colores[1]?>"></label>
                                                            <input type="radio" name="colorTarea" id="colorTarea2" value="<?= $colores[1]?>" <?php if($tareaOsubtarea["color"]==$colores[1])echo "checked";?> hidden onChange="checkColorTarea(event)">
                                                        </li>
                                                        <li onClick="clickLabelColorTarea(event)">
                                                            <label for="colorTarea3" class="dropdown-item" data-value="<?= $colores[2]?>" style="background-color: <?= $colores[2]?>"></label>
                                                            <input type="radio" name="colorTarea" id="colorTarea3" value="<?= $colores[2]?>" <?php if($tareaOsubtarea["color"]==$colores[2])echo "checked";?> hidden onChange="checkColorTarea(event)">
                                                        </li>
                                                        <li onClick="clickLabelColorTarea(event)">
                                                            <label for="colorTarea4" class="dropdown-item" data-value="<?= $colores[3]?>" style="background-color: <?= $colores[3]?>"></label>
                                                            <input type="radio" name="colorTarea" id="colorTarea4" value="<?= $colores[3]?>" <?php if($tareaOsubtarea["color"]==$colores[3])echo "checked";?> hidden onChange="checkColorTarea(event)">
                                                        </li>
                                                        <li onClick="clickLabelColorTarea(event)">
                                                            <label for="colorTarea5" class="dropdown-item" data-value="<?= $colores[4]?>" style="background-color: <?= $colores[4]?>"></label>
                                                            <input type="radio" name="colorTarea" id="colorTarea5" value="<?= $colores[4]?>" <?php if($tareaOsubtarea["color"]==$colores[4])echo "checked";?> hidden onChange="checkColorTarea(event)">
                                                        </li>
                                                        <li class="labelColorL" onClick="clickLabelColorTarea(event)">
                                                            <label for="colorTarea6" class="dropdown-item" data-value="<?= $colores[5]?>" style="background-color: <?= $colores[5]?>"></label>
                                                            <input type="radio" name="colorTarea" id="colorTarea6" value="<?= $colores[5]?>" <?php if($tareaOsubtarea["color"]==$colores[5])echo "checked";?> hidden onChange="checkColorTarea(event)">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 d-flex flex-column align-items-center">
                                            <label for="fechaRecordatorioTarea" class="col-form-label">Fecha de Recordatorio </label> 
                                            <input type="datetime-local" class="form-control" id="fechaRecordatorioTarea" name="fechaRecordatorioTarea"  <?php
                                            if(old("fechaRecordatorioTarea")!=null) echo "value='".old("fechaRecordatorioTarea")."'";else echo "value=''"?> min="<?php
                                                date_default_timezone_set("America/Argentina/Buenos_Aires");
                                                $hoy=date("U");
                                                echo date("Y-m-d H:i",($hoy+86400));?>" max="<?php 
                                                echo date("Y-m-d H:i",date_format(date_create($fechaTarea),"U")-86400);
                                            ?>">
                                            <p class="mb-0">
                                                <?php if(isset(validation_errors()["fechaVencimientoSubTarea"])){
                                                        echo str_replace("fechaVencimientoSubTarea","Fecha de Vencimiento",validation_errors()["fechaVencimientoSubTarea"]);
                                                }?>
                                            </p>
                                        </div>
                                    <?php }elseif($tareaOsubtarea["autor"]!=session()->get("usuario")["id"] && $tareaOsubtarea["tipoTC"]==2){?>
                                        <div class="mb-3">
                                            <label for="descripcionViejaTarea" class="col-form-label">Descripcion</label>
                                            <textarea class="form-control" id="descripcionViejaTarea" disabled><?= $tareaOsubtarea["descripcion"]?></textarea>
                                            <label for="descripcionTarea" class="col-form-label">Anexar</label>
                                            <textarea class="form-control" name="descripcionTarea" id="descripcionTarea"><?php if(old("descripcionTarea")!=null) echo old("descripcionTarea");?></textarea>
                                            <p class="mb-0">
                                                <?php if(isset(validation_errors()["descripcionTarea"])){
                                                        echo str_replace("descripcionTarea","Descripcion",validation_errors()["descripcionTarea"]);
                                                }?>
                                            </p>
                                        </div>
                                    <?php }?>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="reset" form="modTarea" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" form="modTarea" class="btn btn-primary">Modificar</button>
                            </div>
                            </div>
                        </div>
                    </div>
                    <?php if($tareaOsubtarea["estado"]!=3 && $tareaOsubtarea["autor"]==session()->get("usuario")["id"]){?>
                        <div class="modal fade" id="compartirTareaModal" tabindex="-1" aria-labelledby="compartirTareaModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content dark">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="compartirTareaModalLabel">Compartir Tarea</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="compartirTarea" action="<?= base_url($urlTarea."/sharetarea")?>" method="post">
                                        <div class="mb-3 d-flex flex-column align-items-start justify-content-center">
                                            <label for="usuarioCompartirTarea" class="col-form-label">Usuario</label> 
                                            <input type="text" class="form-control" id="usuarioCompartirTarea" name="usuarioCompartirTarea" placeholder="Nombre de usuario" <?php if(old("usuarioCompartirTarea")!=null) echo "value='".old("usuarioCompartirTarea")."'"?>>
                                            <p class="mb-0">
                                                <?php $mensaje=session()->getFlashdata("mensaje"); 
                                                if(isset(validation_errors()["usuarioCompartirTarea"])){
                                                    echo str_replace("usuarioCompartirTarea","Usuario",validation_errors()["usuarioCompartirTarea"]);
                                                }elseif(isset($mensaje["usuarioCompartirTarea"])){
                                                    echo $mensaje["usuarioCompartirTarea"];
                                                }?>
                                            </p>
                                        </div>
                                        <div class="mb-3 d-flex flex-column align-items-start justify-content-center">
                                            <label for="accesibilidadCompartirTarea" class="col-form-label">Accesibilidad</label> 
                                            <select name="accesibilidadCompartirTarea" id="accesibilidadCompartirTarea">
                                                <option value="1" selected>Lectura</option>
                                                <option value="2">Escritura</option>
                                                <option value="3">Modificacion</option>
                                            </select>
                                        </div>
                                        <input type="text" name="idTarea" value="<?= $idTarea?>" hidden>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="reset" form="compartirTarea" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" form="compartirTarea" class="btn btn-primary">Compartir</button>
                                </div>
                                </div>
                            </div>
                        </div>

                    <?php }?>
                    <?php break;}else{
                        continue;
                        }}?>
                </div>
            </div>
            <div class="crearSubTarea d-flex align-items-center justify-content-between mt-3 mb-2">
                <div class="col-6 d-flex align-items-center">
                    <?php $mensaje=session()->getFlashdata("mensaje"); ?>
                    <h5 class="dark m-0 ms-4 border-bottom border-<?php if(isset($mensaje["success"]))echo "success";elseif(isset($mensaje["success"])) echo "danger";?>"><?php if(isset($mensaje["mensaje"])) echo $mensaje["mensaje"];?></h5>
                </div>
                <?php if(isset($visibilidadTarea))if($visibilidadTarea["estado"]<3 && session()->get("usuario")["id"]==$visibilidadTarea["autor"]){?>
                    <button type="button" id="botonNewSubTarea" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newSubTareaModal">Nueva subTarea</button>
                    <div class="modal fade" id="newSubTareaModal" tabindex="-1" aria-labelledby="newSubTareaModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content dark">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="newSubTareaModalLabel">Nueva SubTarea</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="newSubTarea" action="<?= base_url()."tarea/newsubtarea"?>" method="post">
                                    <div class="mb-3">
                                        <label for="descripcionSubTarea" class="col-form-label">Descripcion</label>
                                        <textarea class="form-control" name="descripcionSubTarea" id="descripcionSubTarea"><?php if(old("descripcionSubTarea")!=null) echo old("descripcionSubTarea")?></textarea>
                                        <p class="mb-0">
                                            <?php if(isset(validation_errors()["descripcionSubTarea"])){
                                                    echo str_replace("descripcionSubTarea","Descripcion",validation_errors()["descripcionSubTarea"]);
                                            }?>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comentarioSubTarea" class="col-form-label">Comentario(Opcional)</label>
                                        <textarea class="form-control" name="comentarioSubTarea" id="comentarioSubTarea"><?php if(old("comentarioSubTarea")!=null) echo old("comentarioSubTarea");?></textarea>
                                        <p class="mb-0">
                                            <?php if(isset(validation_errors()["comentarioSubTarea"])){
                                                    echo str_replace("comentarioSubTarea","Comentario",validation_errors()["comentarioSubTarea"]);
                                            }?>
                                        </p>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-around">
                                        <div class="col-5 d-flex">
                                            <label for="prioridadSubTarea" class="col-form-label">Prioridad </label>
                                            <select class="ms-2 form-control" name="prioridadSubTarea" id="prioridadSubTarea" <?php if(old("prioridadSubTarea")!=null) echo "value='".old("prioridadSubTarea")."'"?>>
                                                <option value="1"></option>
                                                <option value="2">Baja</option>
                                                <option value="3">Normal</option>
                                                <option value="4">Alta</option>
                                            </select>
                                        </div>
                                        <?php $colores=["#6f3c1e5c","#7820695c","#4016645c","#2805555c","#276d345c","#035f785c"]?>
                                        <div class="col-5 d-flex">
                                            <label for="colorSubTarea" class="col-form-label">Color </label>
                                            <div class="dropdown colorSubTarea">
                                                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><div></div></button>
                                                <ul class="dropdown-menu dark">
                                                    <li class="labelColorF" onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea1" class="dropdown-item" data-value="<?= $colores[0]?>" style="background-color: <?= $colores[0]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea1" value="<?= $colores[0]?>" checked hidden onChange="checkColor(event)">
                                                    </li>
                                                    <li onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea2" class="dropdown-item" data-value="<?= $colores[1]?>" style="background-color: <?= $colores[1]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea2" value="<?= $colores[1]?>" hidden onChange="checkColor(event)">
                                                    </li>
                                                    <li onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea3" class="dropdown-item" data-value="<?= $colores[2]?>" style="background-color: <?= $colores[2]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea3" value="<?= $colores[2]?>" hidden onChange="checkColor(event)">
                                                    </li>
                                                    <li onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea4" class="dropdown-item" data-value="<?= $colores[3]?>" style="background-color: <?= $colores[3]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea4" value="<?= $colores[3]?>" hidden onChange="checkColor(event)">
                                                    </li>
                                                    <li onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea5" class="dropdown-item" data-value="<?= $colores[4]?>" style="background-color: <?= $colores[4]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea5" value="<?= $colores[4]?>" hidden onChange="checkColor(event)">
                                                    </li>
                                                    <li class="labelColorL" onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea6" class="dropdown-item" data-value="<?= $colores[5]?>" style="background-color: <?= $colores[5]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea6" value="<?= $colores[5]?>" hidden onChange="checkColor(event)">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-around align-items-center">
                                        <div class="mb-3 d-flex flex-column align-items-start justify-content-center">
                                            <label for="responsableSubTarea" class="col-form-label">Responsable(Opcional)</label> 
                                            <input type="text" class="form-control" id="responsableSubTarea" name="responsableSubTarea" placeholder="Nombre de usuario" <?php if(old("responsableSubTarea")!=null) echo "value='".old("responsableSubTarea")."'"?>>
                                            <p class="mb-0">
                                                <?php if(isset(validation_errors()["responsableSubTarea"])){
                                                        echo str_replace("responsableSubTarea","Responsable",validation_errors()["responsableSubTarea"]);
                                                }elseif(isset($mensaje["responsableSubTarea"])){
                                                    echo $mensaje["responsableSubTarea"];
                                                }?>
                                            </p>
                                        </div>
                                        <div class="mb-3 d-flex flex-column align-items-center">
                                            <label for="fechaVencimientoSubTarea" class="col-form-label">Fecha de Vencimiento(Opcional)</label> 
                                            <input type="datetime-local" class="form-control" id="fechaVencimientoSubTarea" name="fechaVencimientoSubTarea"  <?php
                                            if(old("fechaVencimientoSubTarea")!=null) echo "value='".old("fechaVencimientoSubTarea")."'"?> min="<?php
                                                date_default_timezone_set("America/Argentina/Buenos_Aires");
                                                $hoy=date("U");
                                                echo date("Y-m-d H:i",($hoy+86400));?>" max="<?php 
                                                echo date("Y-m-d H:i",date_format(date_create($fechaTarea),"U")-86400);
                                            ?>">
                                            <p class="mb-0">
                                                <?php if(isset(validation_errors()["fechaVencimientoSubTarea"])){
                                                        echo str_replace("fechaVencimientoSubTarea","Fecha de Vencimiento",validation_errors()["fechaVencimientoSubTarea"]);
                                                }?>
                                            </p>
                                        </div>
                                    </div>
                                    <input type="number" name="idTarea" value="<?= $idTarea?>" hidden>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="reset" form="newSubTarea" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" form="newSubTarea" class="btn btn-primary">Crear</button>
                            </div>
                            </div>
                        </div>
                    </div>
                <?php }?>
                <div class="dropdown ordenPagina">
                    <button class="btn dropdown-toggle dark" type="button" data-bs-toggle="dropdown" aria-expanded="false"><?php switch(session()->get("orden")){ case 1: echo "Recientes";break; case 2: echo "Prioridad";break; case 3: echo "Vencimiento"; default: echo "Recientes";}?></button>
                    <ul class="dropdown-menu dark">
                        <li>
                            <a class="dropdown-item text-reset text-decoration-none" href="<?php  echo base_url().$urlTarea."/".$idTarea."/1";?>">Recientes</a></label>
                        </li>
                        <li>
                            <a class="dropdown-item text-reset text-decoration-none" href="<?php  echo base_url().$urlTarea."/".$idTarea."/2";?>">Prioridad</a>
                        </li>
                        <li>
                            <a class="dropdown-item text-reset text-decoration-none" href="<?php  echo base_url().$urlTarea."/".$idTarea."/3";?>">Vencimiento</a>
                        </li>
                    </ul>
                </div>
                
            </div>
            <div id="subTareasDiv" class="list dark">
                <?php if(isset($tarea_subTareas)){
                        foreach($tarea_subTareas as $tareaOsubtarea){
                            if($tareaOsubtarea["tarea_subtarea"]=="subtarea"){?>
                        <div class="subTarea col-10 prioriSubTarea_<?= $tareaOsubtarea["prioridad"] ?>" style="background-color:<?= $tareaOsubtarea["color"]?>;">
                            <a class="col-12 text-reset text-decoration-none" href="<?= base_url().$urlTarea."/subtarea/".(array_find_key($ids,function($value)use($tareaOsubtarea){return $value===$tareaOsubtarea["id"];})+1);?>">
                                <div class="descripcionSubTarea py-2 px-2"><p class="dark m-0"><?= $tareaOsubtarea["descripcion"]?></p></div>
                                <div class="fechaEstadoSubTarea">
                                    <div class="d-flex">
                                        <p class="dark fvt"><?= substr($tareaOsubtarea["fechaVencimiento"],0,-3)?></p>
                                    </div>
                                    <p class="dark et"><?php 
                                        if($tareaOsubtarea["estado"] == "1") echo "Definida";elseif($tareaOsubtarea["estado"] == "2") echo "En proceso";elseif($tareaOsubtarea["estado"] == "3") echo "Finalizada";
                                    ?></p>
                                </div>
                            </a>
                        </div>
                <?php }else continue;}}?>
            </div>
        </div>
    </section>
    
    
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
<script>
    window.addEventListener("load",()=>{
        var validation=<?= json_encode(array_keys(validation_errors()))?>;
        var shareTarea=<?php if(isset($mensaje["usuarioCompartirTarea"]))echo json_encode(1);else echo json_encode(0);?>;
        var newSubTarea=<?php if(in_array("descripcionSubTarea",array_keys(validation_errors()))||in_array("fechaVencimientoSubTarea",array_keys(validation_errors()))||in_array("responsableSubTarea",array_keys(validation_errors()))||in_array("comentarioSubTarea",array_keys(validation_errors())))echo json_encode(1);else echo json_encode(0);?>;
        var modTarea=<?php if(in_array("fechaRecordatorioTarea",array_keys(validation_errors()))||in_array("tituloTarea",array_keys(validation_errors()))||in_array("descripcionTarea",array_keys(validation_errors())))echo json_encode(1);else echo json_encode(0);?>;
        if(validation.length>0){
            if(validation[0].trim()=="usuarioCompartirTarea"){
                document.querySelector("#botonCompartirTarea").click();
            }else if(newSubTarea==1){
                document.querySelector("#botonNewSubTarea").click();
            }else if(modTarea==1){
                document.querySelector("#botonModTarea").click();
            }
        }else if(shareTarea==1){
            document.querySelector("#botonCompartirTarea").click();
        }
    });
    window.addEventListener("load",()=>{
        var liRadioColor=document.querySelector("#newSubTarea .colorSubTarea label[for='"+document.querySelector("#newSubTarea .colorSubTarea input[type='radio']:checked").id+"']").parentElement;
        liRadioColor.setAttribute("style","border: solid 0.01rem #6c757d");
        var liRadioColor=document.querySelector("#modTarea .colorTarea label[for='"+document.querySelector("#modTarea .colorTarea input[type='radio']:checked").id+"']").parentElement;
        liRadioColor.setAttribute("style","border: solid 0.01rem #6c757d");
        
    });
    function dark_light(e){
        if(e.target.getAttribute("alt").trim()=="dark"){
            e.target.setAttribute("src","https://img.icons8.com/?size=100&id=7clddn7MmGRu&format=png&color=000000");
            e.target.setAttribute("alt","light");
            var cosas=document.querySelectorAll(".dark");
            for (let i = 0; i < cosas.length; i++) {
                cosas[i].classList.remove('dark');
                cosas[i].classList.add('light');
            }
        }else if(e.target.getAttribute("alt").trim()=="light"){
            e.target.setAttribute("src","https://img.icons8.com/?size=100&id=8gmhfnYGKE8G&format=png&color=000000");
            e.target.setAttribute("alt","dark");
            var cosas=document.querySelectorAll(".light");
            for (let i = 0; i < cosas.length; i++) {
                cosas[i].classList.remove('light');
                cosas[i].classList.add('dark');
            }
        }
    }
    function clickLabelColorSubTarea(e){
        if(e.target.firstElementChild!=null)e.target.firstElementChild.click();
    }
    function checkColor(e){
        var liRadioColor=e.target.parentElement;
        var prevCheck=document.querySelector("#newSubTarea .colorSubTarea li[style='border: solid 0.01rem #6c757d']");
        if(!prevCheck.isEqualNode(liRadioColor)){
            prevCheck.setAttribute("style","");
            liRadioColor.setAttribute("style","border: solid 0.01rem #6c757d");
            liRadioColor.parentElement.previousElementSibling.firstChild.setAttribute("style","background-color:"+e.target.previousElementSibling.getAttribute("data-value"));
        }
    }
    function clickLabelColorTarea(e){
        if(e.target.firstElementChild!=null)e.target.firstElementChild.click();
    }
    function checkColorTarea(e){
        var liRadioColor=e.target.parentElement;
        var prevCheck=document.querySelector("#modTarea .colorTarea li[style='border: solid 0.01rem #6c757d']");
        if(!prevCheck.isEqualNode(liRadioColor)){
            prevCheck.setAttribute("style","");
            liRadioColor.setAttribute("style","border: solid 0.01rem #6c757d");
            liRadioColor.parentElement.previousElementSibling.firstChild.setAttribute("style","background-color:"+e.target.previousElementSibling.getAttribute("data-value"));
        }
    }
</script>
</html>
