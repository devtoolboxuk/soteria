<?php

namespace soteria\secure\classes;

use soteria\secure\handlers\Handler;

class Secure extends AbstractSecurity
{
    protected $handlers = [];
    protected $references = [];

    function __construct()
    {
        parent::__construct();
    }

    public function __call($method, $arguments = [])
    {
        $handlers = new Handler($arguments);
        $handler = $handlers->build($method, $arguments);
        $this->pushHandler($handler);
        $this->processHandler();
        return $this->result;
    }
}