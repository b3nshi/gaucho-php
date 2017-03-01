<?php
require_once __DIR__.'/vendor/autoload.php';

# TODO: create the autoload function for classes
include_once('gaucho/routes.php');
include_once('gaucho/response.php');
include_once('gaucho/gaucho.php');

# Config file
# include_once __DIR__.'/config.php';

# Check if user is logged in
include_once ('config.php');
include_once ('src/hooks/jwtAuth.php');

$app = new Gaucho\Gaucho();

try {
  $app->db = new PDO(
    'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['schema'],
    $config['db']['user'],
    $config['db']['password']
  );
} catch (PDOException $e) {
  echo 'Connection fails' . $e->getMessage();
}

$app->before(function () use ($app) {
  # Get the headers to get the token
  $cookies = $app->params->cookies;
  $headers = $app->params->headers;

  #$token = $cookies->has($app['Config']['jwt']['cookie']['name']) ? $cookies->get($app['Config']['jwt']['cookie']['name']) :
  #   (isset($headers[$app['Config']['jwt']['cookie']['name']]) ? $headers[$app['Config']['jwt']['cookie']['name']][0] : '');

  # Get the name of the controller
  // $matches = [];
  // preg_match_all('/([a-z]+)\//i', $request->getPathInfo(), $matches)[1];

  // if (isset($matches[1])) {
  //   $matches = $matches[1];
  // }

  // # Private endpoints should be located under '/p/' path
  // $private = ($matches[0] === 'p');

  // if ($private &&
  //     (empty($token) ||
  //     (!empty($token) && (!JwtAuth::jwtCheck($token))))) {
  //   if (!empty($token)) {
  //     $app->UserLogged = 'invalid';
  //   }
  //   return new Response('Unauthorized', 401);
  // } else if (($private || ($matches[0] === 'login')) && !empty($token)) {
  //   // TODO: Read the globaltestnum from the token and avoid sending it with the request
  //   # Store the user data on the UserLogged object to be used by other endpoints
  //   $app->UserLogged = JwtAuth::jwtGetData($token);
  // }
});

include_once('src/controllers/public.php');

$app->get('/probando/:param', function ($param) {
  return new Gaucho\Response('HELLO my friend, your param is: ' . $param, 500);
});

$app->get('/segundo', function () {
  return 'Second method without params';
});

$app->run();
