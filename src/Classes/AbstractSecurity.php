<?php

namespace soteria\secure\classes;

abstract class AbstractSecurity
{

    public $rules = [];

    protected $result;


    public function __construct()
    {
    }

    public function pushHandler($handler)
    {
        array_unshift($this->handlers, $handler);
        return $this;
    }


    public function processHandler()
    {

        foreach ($this->handlers as $handler) {
            $this->processWrappers($handler);
        }
    }


    protected function processWrappers($handler)
    {
        $value = '';

        $wrappers = array_reverse($handler->getWrappers());

        foreach ($wrappers as $wrapper) {
           if ($value == '') {
               $value = $handler->getValue();
           }
            $wrapper->setValue($value);
            $wrapper->process();

            $value = $wrapper->getValue();
            $this->setResult($value);
        }
    }


    protected function setResult( $result)
    {
        $this->result = $result;
        return $this;
    }

}