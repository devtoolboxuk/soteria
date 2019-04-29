<?php

namespace devtoolboxuk\soteria;

use devtoolboxuk\soteria\Models\SoteriaModel;

abstract class AbstractService
{
    private static $instance = null;
    protected $result;

    protected function processHandler()
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

            $wrapper->setPassArray($handler->getPassArray());
            $wrapper->setValue($value);
            $wrapper->process();

            $value = $wrapper->getValue();
            $this->setResult($value);
        }
    }

    protected function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    protected function process()
    {
        foreach ($this->handlers as $handler) {
            array_unshift($this->references, ['name' => $handler->getName(), 'value' => $handler->getValue()]);
            $this->processWrappers($handler);
        }

        if (self::$instance === null) {
            self::$instance = new SoteriaModel($this->result);
        }

        return self::$instance;
    }

    protected function resetHandler()
    {
        $this->references = [];
        $this->handlers = [];
        self::$instance = null;
        return $this;
    }

}