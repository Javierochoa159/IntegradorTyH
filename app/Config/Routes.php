<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Inicio::index');
$routes->get('/inicio', 'Inicio::index');
$routes->get('/inicio/logout', 'Inicio::logout');
$routes->get('/inicio/todas', 'Inicio::todas');
$routes->get('/inicio/todas/(:num)', 'Inicio::todas/$1');
$routes->get('/inicio/mis_tareas', 'Inicio::misTareas');
$routes->get('/inicio/mis_tareas/(:num)', 'Inicio::misTareas/$1');
$routes->get('/inicio/historial', 'Inicio::historial');
$routes->get('/inicio/historial/(:num)', 'Inicio::/historial/$1');
$routes->get('/inicio/tareas_compartidas', 'Inicio::tareasCompartidas');
$routes->get('/inicio/tareas_compartidas/(:num)', 'Inicio::tareasCompartidas/$1');
$routes->get('/inicio/tarea/(:num)', 'Inicio::tarea/$1');
$routes->get('/inicio/sub_tarea/(:num)', 'Inicio::subTarea/$1');
$routes->post('/inicio/newtarea', 'Inicio::newTarea');

$routes->get('/login', 'Login::index');
$routes->post('/login/exito', 'Login::exito');

$routes->get('/registrar', 'Registrar::index');
$routes->post('/registrar/exito', 'Registrar::exito');

$routes->get("/integradortyhdb","IntegradorTyHDB::index");

$routes->get('/tarea/(:num)', 'Tarea::index/$1');
$routes->get('/tarea/(:num)/(:num)', 'Tarea::todas/$1/$2');
$routes->post('/tarea/newsubtarea', 'Tarea::newSubTarea');
$routes->get('/tarea/estadotarea/(:num)', 'Tarea::setEstadoTarea/$1');
$routes->post('/tarea/modtarea/(:num)', 'Tarea::modTarea/$1');
$routes->post('/tarea/anextarea/(:num)', 'Tarea::anexTarea/$1');
$routes->get('/tarea/subtarea/(:num)', 'Tarea::subTarea/$1');
$routes->post('/tarea/sharetarea', 'Tarea::shareTarea');
$routes->get('/tarea/procesarshare/(:num)/(:num)', 'Tarea::procesarShare/$1/$2');
$routes->get('/tarea/archivartarea/(:num)', 'Tarea::archivarTarea/$1');

$routes->get('/subtarea/(:num)', 'SubTarea::index/$1');
$routes->post('/subtarea/newcomentario', 'SubTarea::newComentario');
$routes->get('/subtarea/estadosubtarea/(:num)', 'SubTarea::setEstadoSubTarea/$1');
$routes->post('/subtarea/modsubtarea/(:num)', 'SubTarea::modSubTarea/$1');
$routes->post('/subtarea/sharesubtarea', 'SubTarea::shareSubTarea');
$routes->get('/subtarea/procesarshare/(:num)/(:num)', 'SubTarea::procesarShare/$1/$2');
$routes->get('/subtarea/procesarresponsable/(:num)/(:num)', 'SubTarea::procesarResponsable/$1/$2');
$routes->get('/subtarea/procesarresponsable/(:num)/(:num)/(:num)', 'SubTarea::procesarResponsable/$1/$2/$3');

$routes->get('/historial/(:num)', 'Historial::index/$1');
$routes->get('/historial/subtarea/(:num)', 'Historial::subTarea/$1');