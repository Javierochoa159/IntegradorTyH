<html><head><title>My Form</title></head><body>
<h3>Envío de Formulario Exitoso!</h3>
<?php
echo "Correo: ".$email."<br>";
echo "Password: ".$password."<br>";
?>
<p>
<?php echo anchor('form', 'Intentar nuevamente!'); ?>
</p> </body> </html>