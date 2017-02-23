<?php

# TODO: create the autoload function for classes
include_once('gaucho/routes.php');
include_once('gaucho/gaucho.php');

$gaucho = new Gaucho\Gaucho();

$gaucho->get('/probando/:param', function ($param) {
  echo 'HELLO, your param is: ' . $param;
});

$gaucho->get('/segundo', function () {
  echo 'Second method without params';
});


# Create group of routes and then mount them under a path
$routes = new Gaucho\Routes('/optional');
$routes->get('/cuarto', function () {
  echo 'Cuarto under third';
});
$gaucho->mount('/tercero', $routes());

$gaucho->run();
