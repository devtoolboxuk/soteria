<?php

namespace devtoolboxuk\soteria;

use devtoolboxuk\soteria\handlers\Handler;

class SoteriaService extends AbstractService implements SoteriaInterface
{

    protected $handlers = [];
    protected $references = [];

    public function __call($method, $arguments = [])
    {
        $handlers = new Handler($arguments);
        $handler = $handlers->build($method, $arguments);
        $this->pushHandler($handler);
        $this->processHandler();
        return $this;
    }

    public function getResult()
    {
        $result = $this->process()->getResult();
        $this->resetHandler();
        return $result;
    }


    public function pushHandler($handler)
    {
        array_unshift($this->handlers, $handler);
        return $this;
    }


}