<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= substr(base_url(),0,-17) ?>Plantilla/Css/login.css">
    <title>Login</title>
</head>
<body>
    <div class="col-12 d-flex flex-column justify-content-center align-items-center primerDiv">
        <div class="col-6 espacioAux">
            <p>
                <?php $mensajes=session()->getFlashdata("errors");
                    if(isset($mensajes["errorLogin"])){
                    echo $mensajes["errorLogin"];
                }?>
            </p>
        </div>
        <form action="<?= base_url()."login/exito"?>" id="formLogin" method="post" class="col-5 d-flex flex-column align-items-center justify-content-center border border-dark rounded-3">
            <div class="col-auto d-flex flex-column align-items-center">
                <div class="col-12 p-2 d-flex flex-column align-items-center">
                    <div class="col-12 d-flex flex-column align-items-star justify-content-center">
                        <label class="fs-3" for="email">Ingrese su correo</label>
                        <input class="fs-4" type="email" name="email" <?php if(old("email")!=null) echo "value='".old("email")."'" ?> placeholder="ejemplo@email.com">
                        <p class="mb-0">
                            <?php if(isset(validation_errors()["email"])){
                                    echo str_replace("email","Correo",validation_errors()["email"]);
                            }elseif(isset($mensajes["email"])){
                                echo $mensajes["email"];
                            }?>
                        </p>
                    </div>
                    <div class="col-12 pt-2 d-flex flex-column align-items-star justify-content-center">
                        <label class="fs-3" for="pass">Ingrese su contraseña</label>
                        <input class="fs-4" type="password" name="pass">
                        <p class="mb-0">
                            <?php if(isset(validation_errors()["pass"])){
                                    echo str_replace("pass","Contraseña",validation_errors()["pass"]);
                            }elseif(isset($mensajes["pass"])){
                                echo $mensajes["pass"];
                            }?>
                        </p>
                    </div>
                </div>
                <div class="col-12 p-2 pb-2 d-flex justify-content-end">
                    <input class="fs-3" type="submit" value="Iniciar Sesion">
                </div>
                <div class="col-12 pe-2 mb-4 d-flex justify-content-end">
                    <a href="<?= base_url() ?>registrar">Crear una cuenta</a>
                </div>
            </div>
        </form>
        <div class="col-6 espacioAux"></div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
</html>
