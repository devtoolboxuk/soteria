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

    protected function processWrappersx($handler)
    {
        $options = $this->getOption('Detection');

        foreach ($handler->getWrappers() as $wrapper) {

            $wrapper->setOptions($handler->getValue(), $options['Rules']);
            $wrapper->process();
            $this->addResult($wrapper->getScore(), $wrapper->getResult());
        }
    }

    protected function processWrappers($handler)
    {
        foreach ($handler->getWrappers() as $wrapper) {
            $wrapper->setValue($handler->getValue());
            $wrapper->process();
            $this->setResult($wrapper->getValue());
        }
    }


    protected function setResult( $result)
    {
        $this->result = $result;
        return $this;
    }

}