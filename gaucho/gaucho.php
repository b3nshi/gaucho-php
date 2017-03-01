<?php
namespace Gaucho;

# TODO: PUT and DELETE params
# TODO: Response codes
# TODO: Handle errors
# TODO: Error pages
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

  private $settings = [
    'sanitize' => true, # True by default, but disabling the performance could be better
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
    if (!empty($basePath) && ($basePath !== '/')) {
      if ($basePath[0] !== '/') {
        # Add slash at the beginning
        $basePath = '/' . $basePath;
      }
      $this->mountPoints[$basePath] = $routes;
    }
  }

  public function run() {
    # Object to store the Response
    $response;

    # Sanitize params if the option is true
    if ($this->settings['sanitize']) {
      $this->params = [
        'get' => filter_input_array($_GET, FILTER_SANITIZE_STRING),
        'post' => filter_input_array($_POST, FILTER_SANITIZE_STRING),
        # If the server is nginx we need to define this function
        'headers' => filter_input_array(getallheaders(), FILTER_SANITIZE_STRING),
        'cookies' => filter_input_array($_COOKIE, FILTER_SANITIZE_STRING),
      ];
    }

    # This array could change if match with a mount point
    $routes = $this->routes[$this->request['type']];
    $path = $this->request['path'];

    # Execute global middleware before
    $this->execArrayFunctions($this->middlewares['before']);

    # First I need to check if we have mount points
    $middlewares = [
      'before' => [],
      'after' => [],
    ];

    if (count($this->mountPoints) > 0) {
      foreach ($this->mountPoints as $key => $value) {
        if (strpos($path, $key) === 0) {
          $routes = $value['routes'][$this->request['type']];
          $middlewares = $value['middlewares'];
          $path = str_replace($key, '', $path);
          break;
        }
      }
    }

    # Now I will control if the route is valid
    foreach ($routes as $route) {
      if ((($path !== '/') && preg_match($route['regex'], $path)) ||
          (($path === '/') && ($route['path'] === $this->default))) {
        # Execute middlewares before for specific route
        $this->execArrayFunctions($middlewares['before']);

        $params = [];
        preg_match_all($route['regex'], $path, $params);
        if (count($params) > 1) {
          $response = call_user_func_array($route['cb'], $params[1]);
        } else {
          $response = $route['cb']();
        }

        # Execute middlewares after for specific route
        $this->execArrayFunctions($middlewares['after']);
        break;
      }
    }

    # Call to the print function or just print the response
    if ($response instanceof Response) {
      $response->print();
    } else if (!empty($response)) {
      echo $response;
    }

    # Execute global middlewares after
    $this->execArrayFunctions($this->middlewares['after']);
  }

  private function execArrayFunctions($functions) {
    foreach ($functions as $func) {
      $func();
    }
  }
}
