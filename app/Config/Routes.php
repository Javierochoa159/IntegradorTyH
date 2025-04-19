<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Inicio::index');
$routes->get('/inicio', 'Inicio::index');
$routes->get('/inicio/logout', 'Inicio::logout');
$routes->post('/inicio/newtarea', 'Inicio::newTarea');

$routes->get('/login', 'Login::index');
$routes->post('/login/exito', 'Login::exito');

$routes->get('/registrar', 'Registrar::index');
$routes->post('/registrar/exito', 'Registrar::exito');

$routes->get("/integradortyhdb","IntegradorTyHDB::index");

$routes->get('/tareas', 'Tareas::index');
