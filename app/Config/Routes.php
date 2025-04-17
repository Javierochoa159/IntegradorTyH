<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get("/integradortyhdb","IntegradorTyHDB::index");

$routes->get('/tareas', 'Tareas::index');

$routes->get('/', 'Login::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/exito', 'Login::exito');

$routes->get('/successlogin', 'Success::index');

$routes->get('/registrar', 'Registrar::index');
$routes->post('/registrar/exito', 'Registrar::exito');
