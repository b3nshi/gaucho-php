<?php

# TODO: create the autoload function for classes
include_once('gaucho/routes.php');
include_once('gaucho/response.php');
include_once('gaucho/gaucho.php');

$gaucho = new Gaucho\Gaucho();

# Global middlewares
$gaucho->before(function () {
  echo 'Before <br/>';
});

$gaucho->after(function () {
  echo 'After <br/>';
});

$gaucho->get('/probando/:param', function ($param) {
  return new Gaucho\Response('HELLO my friend, your param is: ' . $param, 500);
});

$gaucho->get('/segundo', function () {
  return 'Second method without params';
});


# Create group of routes and then mount them under a path
$routes = new Gaucho\Routes('/optional');
$routes->get('/cuarto', function () {
  return 'Cuarto under third';
});
$gaucho->mount('/tercero', $routes());

$gaucho->run();
