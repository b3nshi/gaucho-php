<?php

namespace Gaucho;

class Response
{
  private $data;
  private $code;

  private $templates;

  public function __construct()
  {
    $numargs = func_num_args();
    if ($numargs === 2) {
      $this->data = func_get_arg(0);
      $this->code = func_get_arg(1);
    } else if ($numargs === 1) {
      $arg = func_get_arg(0);
      if (is_string($arg)) {
        $this->data = $arg;
        $this->code = 200;
      } else {
        $this->data = $arg['data'];
        $this->code = $arg['code'];
      }
    }
  }

  public function setCode($code, $data, $htmlCode = 200)
  {
    $this->templates[$code] = [
      'code' => $htmlCode,
      'data' => $data,
    ];
  }

  public function getCode($code)
  {
    return new Response($this->templates[$code]);
  }

  public function print()
  {
    http_response_code($this->code);
    echo $this->data;
  }
}
