<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= substr(base_url(),0,-17) ?>Plantilla/Css/inicio.css">
    <title>Inicio</title>
</head>
<body class="dark">
    <header class="col-12 mb-3 py-4 border-bottom border-dark-subtle d-flex justify-content-between">
        <div class="col-3 ps-3">
            <a href="<?= base_url()."inicio"?>">
            <img src="" alt="logo">
        </a>
    </div>
    <div class="col-3 pe-3 d-flex justify-content-end">
            <div id="dark_light" class="btn btn-primary me-3"><img src="https://img.icons8.com/?size=100&id=8gmhfnYGKE8G&format=png&color=000000" alt="dark" onclick="dark_light(event)"></div>
            <a href="<?= base_url()."inicio/logout"?>" class="dark">Salir</a>
        </div>
    </header>
    <section class="col-12 d-flex">
        <aside class="d-flex flex-column justify-content-start">
            <a href="<?= base_url()."inicio/todas"?>" class="col-12 text-decoration-none text-reset mb-3 bg-azulito<?php if(isset($pagina)) echo "No"; ?> rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="dark py-2 m-0">Tareas</h5>
            </a>
            <a href="<?= base_url()."inicio/mis_tareas"?>" class="col-12 text-decoration-none text-reset mb-3 dark bg-azulito<?php if(!isset($pagina)) echo "No";else if($pagina!=2) echo "No"; ?> rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="dark py-2 m-0">Mis tareas</h5>
            </a>
            <a href="<?= base_url()."inicio/tareas_compartidas"?>" class="col-12 text-decoration-none text-reset mb-3 dark bg-azulito<?php if(!isset($pagina)) echo "No";else if($pagina!=3) echo "No"; ?> rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="dark py-2 m-0">Tareas compartidas</h5>
            </a>
            <a href="<?= base_url()."inicio/historial"?>" class="col-12 text-decoration-none text-reset mb-3 dark bg-azulito<?php if(!isset($pagina)) echo "No";else if($pagina!=4) echo "No"; ?> rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="dark py-2 m-0">Historial</h5>
            </a>
        </aside>
        <div class="principalDiv">
            <div class="col-11 crearTarea d-flex align-items-center justify-content-between">
                <div class="col-6 d-flex align-items-center">
                    <?php $mensaje=session()->getFlashdata("mensaje"); ?>
                    <h5 class="dark m-0 ms-4 border-bottom border-<?php if(isset($mensaje["success"]))echo "success";else echo "danger";?>"><?php if(isset($mensaje["mensaje"])) echo $mensaje["mensaje"];?></h5>
                </div>
                <div id="grid_list" class="btn btn-primary"><img src="https://img.icons8.com/?size=100&id=115942&format=png&color=000000" alt="list" onclick="grid_list(event)"></div>
                <button type="button" id="botonNewTarea" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTareaModal">Nueva tarea</button>
                <div class="dropdown ordenPagina">
                    <button class="btn dropdown-toggle dark btn" type="button" data-bs-toggle="dropdown" aria-expanded="false"><?php switch(session()->get("orden")){ case 1: echo "Recientes";break; case 2: echo "Prioridad";break; case 3: echo "Vencimiento";break; default: echo "recientes";}?></button>
                    <ul class="dropdown-menu dark">
                        <li>
                            <a class="dropdown-item text-reset text-decoration-none" href="<?php $tipoPag=session()->get("pagina"); switch($tipoPag){case 2: echo base_url()."inicio/mis_tareas/1";break; case 3: echo base_url()."inicio/tareas_compartidas/1";break; default: echo base_url()."inicio/todas/1";}?>">Recientes</a></label>
                        </li>
                        <li>
                            <a class="dropdown-item text-reset text-decoration-none" href="<?php switch($tipoPag){case 2: echo base_url()."inicio/mis_tareas/2";break; case 3: echo base_url()."inicio/tareas_compartidas/2";break; default: echo base_url()."inicio/todas/2";}?>">Prioridad</a>
                        </li>
                        <li>
                            <a class="dropdown-item text-reset text-decoration-none" href="<?php switch($tipoPag){case 2: echo base_url()."inicio/mis_tareas/3";break; case 3: echo base_url()."inicio/tareas_compartidas/3";break; default: echo base_url()."inicio/todas/3";}?>">Vencimiento</a>
                        </li>
                    </ul>
                </div>
                <div class="modal fade" id="newTareaModal" tabindex="-1" aria-labelledby="newTareaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content dark">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="newTareaModalLabel">Nueva Tarea</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="newTarea" action="<?= base_url()."inicio/newtarea"?>" method="post">
                            <div class="mb-3">
                                <label for="tituloTarea" class="col-form-label">Titulo</label>
                                <input type="text" class="form-control" name="tituloTarea" id="tituloTarea" <?php if(old("tituloTarea")!=null) echo "value='".old("tituloTarea")."'"?>>
                                <p class="mb-0">
                                    <?php if(isset(validation_errors()["tituloTarea"])){
                                            echo str_replace("tituloTarea","Titulo",validation_errors()["tituloTarea"]);
                                    }?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label for="descripcionTarea" class="col-form-label">Descripcion</label>
                                <textarea class="form-control" name="descripcionTarea" id="descripcionTarea"><?php if(old("descripcionTarea")!=null) echo old("descripcionTarea")?></textarea>
                                <p class="mb-0">
                                    <?php if(isset(validation_errors()["descripcionTarea"])){
                                            echo str_replace("descripcionTarea","Descripcion",validation_errors()["descripcionTarea"]);
                                    }?>
                                </p>
                            </div>
                            <div class="mb-3 d-flex justify-content-around">
                                <div class="col-5 d-flex">
                                    <label for="prioridadTarea" class="col-form-label">Prioridad </label>
                                    <select class="ms-2" name="prioridadTarea" id="prioridadTarea" <?php if(old("prioridadTarea")!=null) echo "value='".old("prioridadTarea")."'"?>>
                                        <option value="1" selected>Baja</option>
                                        <option value="2">Normal</option>
                                        <option value="3">Alta</option>
                                    </select>
                                </div>
                                <?php $colores=["#6f3c1e5c","#7820695c","#4016645c","#2805555c","#276d345c","#035f785c"]?>
                                <div class="col-5 d-flex">
                                    <label for="colorTarea" class="col-form-label">Color </label>
                                    <div class="dropdown colorTarea">
                                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><div></div></button>
                                        <ul class="dropdown-menu dark">
                                            <li class="labelColorF" onClick="clickLabelColorTarea(event)">
                                                <label for="colorTarea1" class="dropdown-item" data-value="<?= $colores[0]?>" style="background-color: <?= $colores[0]?>"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea1" value="<?= $colores[0]?>" checked hidden onChange="checkColor(event)">
                                            </li>
                                            <li onClick="clickLabelColorTarea(event)">
                                                <label for="colorTarea2" class="dropdown-item" data-value="<?= $colores[1]?>" style="background-color: <?= $colores[1]?>"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea2" value="<?= $colores[1]?>" hidden onChange="checkColor(event)">
                                            </li>
                                            <li onClick="clickLabelColorTarea(event)">
                                                <label for="colorTarea3" class="dropdown-item" data-value="<?= $colores[2]?>" style="background-color: <?= $colores[2]?>"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea3" value="<?= $colores[2]?>" hidden onChange="checkColor(event)">
                                            </li>
                                            <li onClick="clickLabelColorTarea(event)">
                                                <label for="colorTarea4" class="dropdown-item" data-value="<?= $colores[3]?>" style="background-color: <?= $colores[3]?>"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea4" value="<?= $colores[3]?>" hidden onChange="checkColor(event)">
                                            </li>
                                            <li onClick="clickLabelColorTarea(event)">
                                                <label for="colorTarea5" class="dropdown-item" data-value="<?= $colores[4]?>" style="background-color: <?= $colores[4]?>"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea5" value="<?= $colores[4]?>" hidden onChange="checkColor(event)">
                                            </li>
                                            <li class="labelColorL" onClick="clickLabelColorTarea(event)">
                                                <label for="colorTarea6" class="dropdown-item" data-value="<?= $colores[5]?>" style="background-color: <?= $colores[5]?>"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea6" value="<?= $colores[5]?>" hidden onChange="checkColor(event)">
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div class="mb-3 d-flex flex-column align-items-center">
                                    <label for="fechaVencimientoTarea">Fecha de Vencimiento </label> 
                                    <input type="datetime-local" id="fechaVencimientoTarea" name="fechaVencimientoTarea"  <?php if(old("fechaVencimientoTarea")!=null) echo "value='".old("fechaVencimientoTarea")."'"?> min="<?php
                                                                                                date_default_timezone_set("America/Argentina/Buenos_Aires");
                                                                                                $hoy=date("U");
                                                                                                echo date("Y-m-d H:i",($hoy+86400));?>" onchange="maxRecorTarea(event)">
                                    <p class="mb-0">
                                        <?php if(isset(validation_errors()["fechaVencimientoTarea"])){
                                                echo str_replace("fechaVencimientoTarea","Fecha de Vencimiento",validation_errors()["fechaVencimientoTarea"]);
                                        }?>
                                    </p>
                                </div>
                                <div class="mb-3 d-flex flex-column align-items-center">
                                    <label for="fechaRecordatorioTarea">Fecha de Recordatorio (opcional) </label> 
                                    <input type="datetime-local" id="fechaRecordatorioTarea" name="fechaRecordatorioTarea" <?php if(old("fechaRecordatorioTarea")!=null) echo "value='".old("fechaRecordatorioTarea")."'"?> min="<?php
                                                                                                date_default_timezone_set("America/Argentina/Buenos_Aires");
                                                                                                $hoy=date("U");
                                                                                                echo date("Y-m-d H:i",($hoy+86400));?>">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" form="newTarea" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" form="newTarea" class="btn btn-primary">Crear</button>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <div id="tareasDiv" class="list dark">
                <?php if(isset($tareas)){
                        foreach($tareas as $tarea){?>
                        <div class="<?php if(!isset($tarea["tarea_subtarea"]))echo $tarea["tarea_subtarea"];else if($tarea["tarea_subtarea"]=="tarea")echo "tarea";else echo"subtarea"?> priori<?php if($tarea["tarea_subtarea"]=="subtarea") echo "Sub";?>Tarea_<?= $tarea["prioridad"] ?>" style="background-color:<?= $tarea["color"]?>;">
                            <a class="col-12 text-reset text-decoration-none" href="<?php $a=base_url()."inicio/";
                                                                                          if(!isset($tarea["tarea_subtarea"]))$a.="tarea/";
                                                                                          else if($tarea["tarea_subtarea"]=="tarea")$a.="tarea/";
                                                                                          else $a.="sub_tarea/";
                                                                                          $a.=(array_find_key($ids,function($value)use($tarea){return $value===$tarea["id"];})+1);
                                                                                          echo $a;
                                                                                          ?>">
                                <div class="tituloTarea py-2 px-2"><h3 class="dark m-0"><?php if(!isset($tarea["tarea_subtarea"])) echo $tarea["titulo"];else if($tarea["tarea_subtarea"]=="subtarea") echo substr($tarea["titulo"],0,30);else echo $tarea["titulo"] ?></h3></div>
                                <div class="fechaEstadoTarea">
                                    <div class="d-flex">
                                        <p class="dark frt"><?php if($tarea["fechaRecordatorio"]!=null) echo substr($tarea["fechaRecordatorio"],0,-3)?></p>
                                        <p class="dark fvt <?php if($tarea["fechaVencimiento"]!=null)if($tarea["fechaVencimiento"]!="")if((date_format(date_create($tarea["fechaVencimiento"]),"U")-date("U"))<=1209600) echo "fechaVence";?>"><?php if($tarea["fechaVencimiento"]!=null)if(date_format(date_create($tarea["fechaVencimiento"]),"U")>0) echo substr($tarea["fechaVencimiento"],0,-3);?></p>
                                    </div>
                                    <p class="dark et"><?php 
                                        switch($tarea["estado"]) {case "1": echo "Definida";break; case "2": echo "En proceso";break; case "3": echo "Finalizada";}
                                    ?></p>
                                </div>
                            </a>
                        </div>
                    <?php }}
                ?>
            </div>
        </div>
    </section>
    
    
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
<script>
    window.addEventListener("load",()=>{
        var validation=<?= json_encode(array_values(validation_errors()))?>;
        if(validation.length>0){
            document.querySelector("#botonNewTarea").click();
        }
    });
    window.addEventListener("load",()=>{
        var liRadioColor=document.querySelector("#newTarea .colorTarea label[for='"+document.querySelector("#newTarea .colorTarea input[type='radio']:checked").id+"']").parentElement;
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
    function grid_list(e){
        if(e.target.getAttribute("alt").trim()=="grid"){
            e.target.setAttribute("src","https://img.icons8.com/?size=100&id=115942&format=png&color=000000");
            e.target.setAttribute("alt","list");
            var tareasDiv=document.querySelector("#tareasDiv");
            tareasDiv.classList.remove('grid');
            tareasDiv.classList.add('list');
        }else if(e.target.getAttribute("alt").trim()=="list"){
            e.target.setAttribute("src","https://img.icons8.com/?size=100&id=69736&format=png&color=000000");
            e.target.setAttribute("alt","grid");
            var tareasDiv=document.querySelector("#tareasDiv");
            tareasDiv.classList.remove('list');
            tareasDiv.classList.add('grid');
        }
    }
    function clickLabelColorTarea(e){
        if(e.target.firstElementChild!=null)e.target.firstElementChild.click();
    }
    function checkColor(e){
        var liRadioColor=e.target.parentElement;
        var prevCheck=document.querySelector("#newTarea .colorTarea li[style='border: solid 0.01rem #6c757d']");
        if(!prevCheck.isEqualNode(liRadioColor)){
            prevCheck.setAttribute("style","");
            liRadioColor.setAttribute("style","border: solid 0.01rem #6c757d");
            liRadioColor.parentElement.previousElementSibling.firstChild.setAttribute("style","background-color:"+e.target.previousElementSibling.getAttribute("data-value"));
        }
    }

    function maxRecorTarea(e){
        document.querySelector("#fechaRecordatorioTarea").setAttribute("min",(Date.parse(e.target.value)-86400));
    }
</script>
</html>