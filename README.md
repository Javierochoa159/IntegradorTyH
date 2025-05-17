# Proyecto Web de Tecnicas y Herramientas con Framework CodeIgniter 4

## Como iniciar el proyecto?

Solo descargar el proyecto por el método que desee. Una ves descargado tendrá que configurar el archivo .env con los siguientes parámetros:

### Configuracion del entorno de desarrollo

 -  CI_ENVIRONMENT = *Ingrese el metodo deseado: development, production, testing (para PHPUnit). ejemplo:* testing

### URL Base del sistema

  - app.baseURL = *URL en donde desea alojar el proyecto, mas /public/index.php/, ejemplo: *http://127.0.0.1/CarpetaRaiz/public/index.php/

### Configuracion de la Base de Datos

  - database.default.hostname = *URL raíz de su base de datos, ejemplo:* 127.0.0.1
  - database.default.database = IntegradorTyH *En este caso tiene que llamarse así solo si no se modifica el nombre dentro del archivo models/IntegradorTyHDBModel.php en la línea 22* 
  - database.default.username = *nombre del usuario de su cuenta en la DB manejada, ejemplo:* root
  - database.default.password = *contraseña de su cuenta en la DB manejada*
  - database.default.DBDriver = MySQLi *Esta configuración esta hecha para funcionar en MySQL, si desea cambiar de servicio haga los cambios pertinentes en la configuración*
  - database.default.DBPrefix = *En este caso no se utiliza este apartado, por lo que se deja comentado con un #, ejemplo: #database.default.DBPrefix. Si desea utilizarlo, remueva el #* 
  - database.default.port = *Puerto utilizado para acceder al servicio de base de datos, ejemplo:* 3306

  - database.aux.hostname = *Misma URL raíz ingresada en database.default.hostname*
  - database.aux.database = *Este espacio debe quedar en blanco para que el sistema pueda acceder a la raíz del servicio de base de datos y no apuntar a una DB en concreto dentro del servicio*
  - database.aux.username = *Mismo nombre de usuario ingresado en database.default.username*
  - database.aux.password = *Misma contraseña ingresada en database.default.password*
  - database.aux.DBDriver = MySQLi
  - database.aux.DBPrefix = *Misma configuracion ingresada en database.default.DBPrefix*
  - database.aux.port = *Mismo puerto ingresado en database.default.port*

### Encriptacion

  - encryption.key = *Elija un conjunto de caracteres del largor deseado para que se haga el cifrado*

## Una ves configurado el .env solo quedara iniciar el servicio hosting con el proyecto en él
