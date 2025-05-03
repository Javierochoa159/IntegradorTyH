<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= substr(base_url(),0,-17) ?>Plantilla/Css/subtarea.css">
    <title>SubTarea</title>
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
            <a href="<?= base_url()."inicio"?>" class="col-12 text-decoration-none text-reset mb-3 bg-azulito<?php if(isset($pagina)) echo "No"; ?> rounded-end-pill d-flex justify-content-start align-items-center">
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
            <div class="col-12 d-flex flex-column align-items-center justify-content-center">
                <div class="col-12 text-start "<?php $mensajeSubTarea=session()->getFlashdata("mensajeSubTarea");?>>
                    <h5 class="dark m-0 ms-4 <?php if(isset($mensajeSubTarea["success"]))echo "border-bottom border-success";elseif(isset($mensajeSubTarea["error"])) echo "border-bottom border-danger";?>"><?php if(isset($mensajeSubTarea["mensaje"])) echo $mensajeSubTarea["mensaje"];?></h5>
                </div>
                <div class="col-11 d-flex justify-content-center">
                    <?php if(isset($subTarea_comentarios)) foreach($subTarea_comentarios as $subTareaOcomentario) {
                        if($subTareaOcomentario["subtarea_comentario"]=="subtarea"){?>
                    <div class="subTarea dark col-10 prioriSubTarea_<?= $subTareaOcomentario["prioridad"]?>" style="background-color:<?= $subTareaOcomentario["color"]?>;">
                        <div class="descripcionSubTarea p-2 mx-2"><p class="dark"><?= $subTareaOcomentario["descripcion"]?></p></div>
                        <div class="pieSubTarea">
                            <p class="dark fvt"><?= substr($subTareaOcomentario["fechaVencimiento"],0,-3)?></p>
                            <p class="dark et"><?php 
                            switch($subTareaOcomentario["estado"]){
                                case 1: echo "Definida";break;
                                case 2: echo "En proceso";break;
                                case 3: echo "Finalizada";break;
                            }
                            ?></p>
                        </div>
                        <div class="dropdown optSubTarea dark">
                            <button class="btn dropdown-toggle dark" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="https://img.icons8.com/?size=100&id=7m1CoJ6JRUqG&format=png&color=000000" alt="options"></button>
                            <ul class="dropdown-menu dark">
                                <li>
                                    <p class="dropdown-item text-reset text-decoration-none mb-0" id="botonModSubTarea" data-bs-toggle="modal" data-bs-target="#modSubTareaModal">Modificar</p>
                                </li>
                                <?php if($subTareaOcomentario["estado"]!=3 && $subTareaOcomentario["autor"]==session()->get("usuario")["id"]){?>
                                <li>
                                    <p id="botonCompartirSubTarea" class="dropdown-item text-reset text-decoration-none mb-0" data-bs-toggle="modal" data-bs-target="#compartirSubTareaModal">Compartir</p>
                                </li>
                                <?php }?>
                                <?php if($subTareaOcomentario["estado"] != "3"){?>
                                <li>
                                    <a class="dropdown-item text-reset text-decoration-none" href="<?php  echo base_url()."subtarea/estadosubtarea/".$idSubTarea;?>"><?php if($subTareaOcomentario["estado"] == "1") echo "Empezar";elseif($subTareaOcomentario["estado"] == "2") echo "Finalizar";?></a>
                                </li><?php }?>
                            </ul>
                        </div>
                        
                    </div>
                    <div class="modal fade" id="modSubTareaModal" tabindex="-1" aria-labelledby="modSubTareaModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content dark">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modSubTareaModalLabel">Modificar SubTarea</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="modSubTarea" action="<?= base_url()."subtarea/modsubtarea/".$idSubTarea?>" method="post">
                                    <div class="mb-3">
                                        <label for="descripcionSubTarea" class="col-form-label">Descripcion</label>
                                        <textarea class="form-control" name="descripcionSubTarea" id="descripcionSubTarea"><?php if(old("descripcionSubTarea")!=null) echo old("descripcionSubTarea");else echo $subTareaOcomentario["descripcion"]?></textarea>
                                        <p class="mb-0">
                                            <?php if(isset(validation_errors()["descripcionSubTarea"])){
                                                    echo str_replace("descripcionSubTarea","Descripcion",validation_errors()["descripcionSubTarea"]);
                                            }?>
                                        </p>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-around">
                                        <div class="col-5 d-flex">
                                            <label for="prioridadSubTarea" class="col-form-label">Prioridad </label>
                                            <select class="ms-2" name="prioridadSubTarea" id="prioridadSubTarea" <?php if(old("prioridadSubTarea")!=null) echo "value='".old("prioridadSubTarea")."'";else echo "value='".$subTareaOcomentario["prioridad"]."'"?>>
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
                                                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><div style="background-color:<?= $subTareaOcomentario["color"]?>;"></div></button>
                                                <ul class="dropdown-menu dark">
                                                    <li class="labelColorF" onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea1" class="dropdown-item" data-value="<?= $colores[0]?>" style="background-color: <?= $colores[0]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea1" value="<?= $colores[0]?>" <?php if($subTareaOcomentario["color"]==$colores[0])echo "checked";?> hidden onChange="checkColorSubTarea(event)">
                                                    </li>
                                                    <li onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea2" class="dropdown-item" data-value="<?= $colores[1]?>" style="background-color: <?= $colores[1]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea2" value="<?= $colores[1]?>" <?php if($subTareaOcomentario["color"]==$colores[1])echo "checked";?> hidden onChange="checkColorSubTarea(event)">
                                                    </li>
                                                    <li onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea3" class="dropdown-item" data-value="<?= $colores[2]?>" style="background-color: <?= $colores[2]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea3" value="<?= $colores[2]?>" <?php if($subTareaOcomentario["color"]==$colores[2])echo "checked";?> hidden onChange="checkColorSubTarea(event)">
                                                    </li>
                                                    <li onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea4" class="dropdown-item" data-value="<?= $colores[3]?>" style="background-color: <?= $colores[3]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea4" value="<?= $colores[3]?>" <?php if($subTareaOcomentario["color"]==$colores[3])echo "checked";?> hidden onChange="checkColorSubTarea(event)">
                                                    </li>
                                                    <li onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea5" class="dropdown-item" data-value="<?= $colores[4]?>" style="background-color: <?= $colores[4]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea5" value="<?= $colores[4]?>" <?php if($subTareaOcomentario["color"]==$colores[4])echo "checked";?> hidden onChange="checkColorSubTarea(event)">
                                                    </li>
                                                    <li class="labelColorL" onClick="clickLabelColorSubTarea(event)">
                                                        <label for="colorSubTarea6" class="dropdown-item" data-value="<?= $colores[5]?>" style="background-color: <?= $colores[5]?>"></label>
                                                        <input type="radio" name="colorSubTarea" id="colorSubTarea6" value="<?= $colores[5]?>" <?php if($subTareaOcomentario["color"]==$colores[5])echo "checked";?> hidden onChange="checkColorSubTarea(event)">
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="reset" form="modSubTarea" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" form="modSubTarea" class="btn btn-primary">Modificar</button>
                            </div>
                            </div>
                        </div>
                    </div>
                    <?php if($subTareaOcomentario["estado"]!=3 && $subTareaOcomentario["autor"]==session()->get("usuario")["id"]){?>
                        <div class="modal fade" id="compartirSubTareaModal" tabindex="-1" aria-labelledby="compartirSubTareaModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content dark">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="compartirSubTareaModalLabel">Compartir SubTarea</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="compartirSubTarea" action="<?= base_url("subtarea/sharesubtarea")?>" method="post">
                                        <div class="mb-3 d-flex flex-column align-items-start justify-content-center">
                                            <label for="usuarioCompartirSubTarea" class="col-form-label">Usuario</label> 
                                            <input type="text" class="form-control" id="usuarioCompartirSubTarea" name="usuarioCompartirSubTarea" placeholder="Nombre de usuario" <?php if(old("usuarioCompartirSubTarea")!=null) echo "value='".old("usuarioCompartirSubTarea")."'"?>>
                                            <p class="mb-0">
                                                <?php $mensaje=session()->getFlashdata("mensaje"); 
                                                if(isset(validation_errors()["usuarioCompartirSubTarea"])){
                                                    echo str_replace("usuarioCompartirSubTarea","Usuario",validation_errors()["usuarioCompartirSubTarea"]);
                                                }elseif(isset($mensaje["usuarioCompartirSubTarea"])){
                                                    echo $mensaje["usuarioCompartirSubTarea"];
                                                }?>
                                            </p>
                                        </div>
                                        <div class="mb-3 d-flex flex-column align-items-start justify-content-center">
                                            <label for="accesibilidadCompartirSubTarea" class="col-form-label">Accesibilidad</label> 
                                            <select name="accesibilidadCompartirSubTarea" id="accesibilidadCompartirSubTarea">
                                                <option value="1" selected>Lectura</option>
                                                <option value="2">Escritura</option>
                                                <option value="3">Modificacion</option>
                                            </select>
                                        </div>
                                        <input type="text" name="idSubTarea" value="<?= $idSubTarea?>" hidden>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="reset" form="compartirSubTarea" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" form="compartirSubTarea" class="btn btn-primary">Compartir</button>
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
            <div class="crearComentario d-flex align-items-center justify-content-between mt-3 mb-2">
                <div class="col-6 d-flex align-items-center">
                    <?php $mensaje=session()->getFlashdata("mensaje"); ?>
                    <h5 class="dark m-0 ms-4 border-bottom border-<?php if(isset($mensaje["success"]))echo "success";elseif(isset($mensaje["success"])) echo "danger";?>"><?php if(isset($mensaje["mensaje"])) echo $mensaje["mensaje"];?></h5>
                </div>
                <button type="button" id="botonNewComentario" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newComentarioModal">Nuevo comentario</button>
                <div class="modal fade" id="newComentarioModal" tabindex="-1" aria-labelledby="newComentarioModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content dark">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="newComentarioModalLabel">Nuevo Comentario</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="newComentario" action="<?= base_url()."subtarea/newcomentario"?>" method="post">
                                    <div class="mb-3">
                                        <label for="comentarioComentario" class="col-form-label">Comentario</label>
                                        <textarea class="form-control" name="comentarioComentario" id="comentarioComentario"><?php if(old("comentarioComentario")!=null) echo old("comentarioComentario")?></textarea>
                                        <p class="mb-0">
                                            <?php if(isset(validation_errors()["comentarioComentario"])){
                                                    echo str_replace("comentarioComentario","Comentario",validation_errors()["comentarioComentario"]);
                                            }?>
                                        </p>
                                    </div>
                                    <input type="number" name="idSubTarea" value="<?= $idSubTarea?>" hidden>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="reset" form="newComentario" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" form="newComentario" class="btn btn-primary">Crear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="comentariosDiv" class="list">
                <?php if(isset($subTarea_comentarios)){
                        foreach($subTarea_comentarios as $subTareaOcomentario){
                            if($subTareaOcomentario["subtarea_comentario"]=="comentario"){?>
                        <div class="comentario dark col-10">
                            <div class="comentarioComentario py-2 px-2"><p class="dark m-0"><?= $subTareaOcomentario["descripcion"]?></p></div>
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
        var validation=<?= json_encode(array_values(validation_errors()))?>;
        var newComErr=<?php if(isset(validation_errors()["descripcionSubTarea"]))echo json_encode(validation_errors()["comentarioComentario"]);else echo json_encode("");?>;
        var mensaje=<?php if(isset($mensaje["responsableSubTarea"])) echo json_encode($mensaje["responsableSubTarea"]);else echo json_encode("")?>;
        if(validation.length>0||mensaje.trim()!=""){
            document.querySelector("#botonModSubTarea").click();
        }else if(newComErr.trim()!==""){
            document.querySelector("#botonNewComentario").click();
        }
    });
    window.addEventListener("load",()=>{
        var liRadioColor=document.querySelector("#modSubTarea .colorSubTarea label[for='"+document.querySelector("#modSubTarea .colorSubTarea input[type='radio']:checked").id+"']").parentElement;
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
    function checkColorSubTarea(e){
        var liRadioColor=e.target.parentElement;
        var prevCheck=document.querySelector("#modSubTarea .colorSubTarea li[style='border: solid 0.01rem #6c757d']");
        if(!prevCheck.isEqualNode(liRadioColor)){
            prevCheck.setAttribute("style","");
            liRadioColor.setAttribute("style","border: solid 0.01rem #6c757d");
            liRadioColor.parentElement.previousElementSibling.firstChild.setAttribute("style","background-color:"+e.target.previousElementSibling.getAttribute("data-value"));
        }
    }
</script>
</html>
