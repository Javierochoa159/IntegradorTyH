<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= base_url() ?>Plantilla/Css/inicio.css">
    <title>Inicio</title>
</head>
<body class="bg-dark">
    <header class="col-12 mb-3 py-4 border-bottom border-dark-subtle d-flex justify-content-between">
        <div class="col-3 ps-3">
            <a href="<?= base_url()."public/index.php/inicio"?>">
                <img src="" alt="logo">
            </a>
        </div>
        <div class="col-3 pe-3 d-flex justify-content-end">
            <a href="<?= base_url()."public/index.php/inicio/logout"?>">Salir</a>
        </div>
    </header>
    <section class="col-12 d-flex">
        <aside class="d-flex flex-column justify-content-start">
            <div class="col-12 mb-3 bg-azulito rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="text-light py-2 m-0">Mi Pagina de Inicio</h5>
            </div>
            <div class="col-12 mb-3 bg-dark rounded-end-pill d-flex justify-content-start align-items-center">
                <div class="col-1"></div>
                <h5 class="text-light py-2 m-0"><?= session()->get("usuario")["id"]."_".session()->get("usuario")["user"] ?></h5>
            </div>
        </aside>
        <div class="principalDiv">
            <div class="crearTarea">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTareaModal">Nueva tarea</button>

                <div class="modal fade" id="newTareaModal" tabindex="-1" aria-labelledby="newTareaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="newTareaModalLabel">Nueva Tarea</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="newTarea" action="<?= base_url()."public/index.php/inicio/newtarea"?>" method="post">
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
                                <textarea class="form-control" name="descripcionTarea" id="descripcionTarea" <?php if(old("descripcionTarea")!=null) echo "value='".old("descripcionTarea")."'"?>></textarea>
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
                                        <option value="1">Baja</option>
                                        <option value="2">Normal</option>
                                        <option value="3">Alta</option>
                                    </select>
                                </div>
                                <div class="col-5 d-flex">
                                    <label for="colorTarea" class="col-form-label">Color </label>
                                    <div class="dropdown colorTarea">
                                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><div></div></button>
                                        <ul class="dropdown-menu">
                                            <li class="labelColorF">
                                                <label for="colorTarea1" class="dropdown-item" data-value="#6f3c1e" style="background-color: #6f3c1e"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea1" value="#6f3c1e" checked hidden onChange="checkColor(event)">
                                            </li>
                                            <li>
                                                <label for="colorTarea2" class="dropdown-item" data-value="#782069" style="background-color: #782069"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea2" value="#782069" hidden onChange="checkColor(event)">
                                            </li>
                                            <li>
                                                <label for="colorTarea3" class="dropdown-item" data-value="#401664" style="background-color: #401664"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea3" value="#401664" hidden onChange="checkColor(event)">
                                            </li>
                                            <li>
                                                <label for="colorTarea4" class="dropdown-item" data-value="#280555" style="background-color: #280555"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea4" value="#280555" hidden onChange="checkColor(event)">
                                            </li>
                                            <li>
                                                <label for="colorTarea5" class="dropdown-item" data-value="#276d34" style="background-color: #276d34"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea5" value="#276d34" hidden onChange="checkColor(event)">
                                            </li>
                                            <li class="labelColorL">
                                                <label for="colorTarea6" class="dropdown-item" data-value="#035f78" style="background-color: #035f78"></label>
                                                <input type="radio" name="colorTarea" id="colorTarea6" value="#035f78" hidden onChange="checkColor(event)">
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
                                                                                                echo date("Y-m-d H:i",($hoy+86400));?>">
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
            <div class="tareasDiv">
                <?php if(isset($tareas)){
                        foreach($tareas as $tarea){?>
                        <div class="tarea prioriTarea_<?= $tarea["prioridadTarea"] ?> d-flex flex-column justify-content-center" style="background-color:<?= $tarea["colorTarea"]?>;">
                            <div><h3 class="text-light"><?= $tarea["tituloTarea"] ?></h3></div>
                            <div class="d-flex justify-content-between">
                                <p class="text-light"><?= explode(" ",$tarea["fechaVencimientoTarea"])[0]?></p>
                                <p class="text-light"><?php 
                                    if($tarea["estadoTarea"] == "1") echo "Definida";else echo "En proceso";
                                ?></p>
                            </div>
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
        var liRadioColor=document.querySelector("#newTarea .colorTarea label[for='"+document.querySelector("#newTarea .colorTarea input[type='radio']:checked").id+"']").parentElement;
        liRadioColor.setAttribute("style","border: solid 0.01rem #6c757d");
        
    });
    function checkColor(e){
        var liRadioColor=e.target.parentElement;
        var prevCheck=document.querySelector("#newTarea .colorTarea li[style='border: solid 0.01rem #6c757d']");
        if(!prevCheck.isEqualNode(liRadioColor)){
            prevCheck.setAttribute("style","");
            liRadioColor.setAttribute("style","border: solid 0.01rem #6c757d");
            liRadioColor.parentElement.previousElementSibling.firstChild.setAttribute("style","background-color:"+e.target.previousElementSibling.getAttribute("data-value"));
        }
    }
</script>
</html>