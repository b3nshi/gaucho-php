<?php
namespace Gaucho;

class Routes
{
  protected $routes = [
    'GET' => [],
    'POST' => [],
    'PUT' => [],
    'DELETE' => [],
    'MATCH' => []
  ];

  protected $middlewares = [
    'before' => [],
    'after' => [],
  ];

  private $basePath;

  function __construct($basePath = '')
  {
    if (!empty($basePath) && ($basePath !== '/')) {
      if ($basePath[0] !== '/') {
        # Add slash at the beginning
        $basePath = '/' . $basePath;
      }
      $this->basePath = $basePath;
    }
  }

  /**
   * Method used to use the object as a function.
   */
  public function __invoke()
  {
    return [
      'routes' => $this->routes,
      'middlewares' => $this->middlewares,
    ];
  }

  private function createRoute($path, $callback)
  {
    if (!empty($path) && ($path !== '/')) {
      if ($path[0] !== '/') {
        # Add slash at the beginning
        $path = '/' . $path;
      }

      # Add the basePath
      $path = $this->basePath . $path;

      # TODO: The params can't be null. Allow empty params
      $regex = str_replace('/', '\/', $path);
      # Just to make this clear I divided in two different steps
      $regex = preg_replace('/\/\:\w+/i', '/(.+)', $regex);
      $params = [];
      preg_match_all('/\/\:(\w+)/i', $path, $params);
      if (count($params) === 2) {
        $params = $params[1];
      }
      return [
        'cb' => $callback,
        'path' => $path,
        'regex' => '/' . $regex . '/i',
        'params' => $params
      ];
    } else {
      # Return an empty array
      return [];
    }
  }

  public function method($type, $path, $callback)
  {
    $this->routes[$type][] = $this->createRoute($path, $callback);
  }

  public function get($path, $callback)
  {
    $this->method('GET', $path, $callback);
  }

  public function post($path, $callback)
  {
    $this->method('POST', $path, $callback);
  }

  public function put($path, $callback)
  {
    $this->method('PUT', $path, $callback);
  }

  public function delete($path, $callback)
  {
    $this->method('DELETE', $path, $callback);
  }

  public function match($path, $callback)
  {
    $this->method('MATCH', $path, $callback);
  }

  public function before($callback)
  {
    $this->middlewares['before'][] = $callback;
  }

  public function after($callback)
  {
    $this->middlewares['after'][] = $callback;
  }
}
