<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= base_url() ?>Plantilla/Css/login.css">
    <title>Registrarse</title>
</head>
<body>
    <div class="col-12 d-flex flex-column justify-content-center align-items-center primerDiv">
        <div class="col-6 espacioAux">
            <p>
                <?php $mensajes=session()->getFlashdata("errors");
                    if(isset($mensajes["errorRegistrar"])){
                    echo $mensajes["errorRegistrar"];
                }?>
            </p>
        </div>
        <form action="<?= base_url()."public/index.php/registrar/exito"?>" id="formRegistrar" method="post" class="col-5 d-flex flex-column align-items-center justify-content-center border border-dark rounded-3">
            <div class="col-auto d-flex flex-column align-items-center">
                <div class="col-12 p-2 pt-4 d-flex flex-column align-items-center">
                    <div class="col-12 d-flex flex-column align-items-star justify-content-center">
                        <label class="fs-3" for="User">Ingrese un nombre</label>
                        <input class="fs-4" id="User" type="text" name="user" <?php if(old("user")!=null) echo "value='".old("user")."'" ?>>
                        <p class="mb-0">
                            <?php if(isset(validation_errors()["user"])){
                                    echo str_replace("user","Usuario",validation_errors()["user"]);
                            }elseif(isset($mensajes["user"])){
                                echo $mensajes["user"];
                            }?>
                        </p>
                    </div>
                    <div class="col-12 d-flex flex-column align-items-star justify-content-center">
                        <label class="fs-3" for="Email">Ingrese un correo</label>
                        <input class="fs-4" id="Email" type="email" name="email" <?php if(old("email")!=null) echo "value='".old("email")."'" ?> placeholder="ejemplo@email.com">
                        <p class="mb-0">
                            <?php if(isset(validation_errors()["email"])){
                                    echo str_replace("email","Correo",validation_errors()["email"]);
                            }elseif(isset($mensajes["email"])){
                                echo $mensajes["email"];
                            }?>
                        </p>
                    </div>
                    <div class="col-12 d-flex flex-column align-items-star justify-content-center">
                        <label class="fs-3" for="Pass">Ingrese una contraseña</label>
                        <input class="fs-4" id="Pass" type="password" name="pass">
                        <p class="mb-0"><?php if(isset(validation_errors()["pass"])){
                                    echo str_replace("pass","Contraseña",validation_errors()["pass"]);
                        }?></p>
                    </div>
                </div>
                <div class="col-12 p-2 pb-2 d-flex justify-content-end">
                    <input class="fs-3" type="submit" value="Registrarse">
                </div>
                <div class="col-12 pe-2 mb-4 d-flex justify-content-end">
                    <a href="<?= base_url() ?>public/index.php/login">Tengo una cuenta</a>
                </div>
            </div>
        </form>
        <div class="col-6 espacioAux"></div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
</html>
