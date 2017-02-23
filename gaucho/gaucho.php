<?php
namespace Gaucho;

class Gaucho extends Routes
{
  public $params = [
    'get' => [],
    'post' => [],
    'headers' => [],
    'cookies' => [],
  ];

  public $request = [
    'type' => 'GET', # GET by Default
    'method' => 'index', # index by Default
    'controller' => '',
    'mount' => '', # Mount point
    'query' => '', # Query string
  ];

  public $mountPoints = [];

  public function __construct() {
    # Remove if its working in localhost the folder name
    $folder = basename(getcwd());

    # I need to read the request params
    $this->request['path'] = str_replace($folder . '/', '', $_SERVER['REQUEST_URI']);
    $this->request['query'] = $_SERVER['QUERY_STRING'];
    $this->request['type'] = strtoupper($_SERVER['REQUEST_METHOD']);
  }

  public function mount($basePath, $routes) {
    $this->routes[$basePath] = $routes;
  }

  public function run() {
    # This array could change if match with a mount point
    $routes = $this->routes[$this->request['type']];
    $path = $this->request['path'];

    # First I need to check if we have mount points
    if (count($this->mountPoints) > 0) {
      # For now do nothing
    }

    # Now I will control if the route is valid
    foreach ($routes as $route) {
      if (preg_match($route['regex'], $path)) {
        $params = [];
        preg_match_all($route['regex'], $path, $params);
        if (count($params) > 1) {
          call_user_func_array($route['cb'], $params[1]);
        } else {
          $route['cb']();
        }
      }
    }
  }
}
