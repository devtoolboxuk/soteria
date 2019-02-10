<?php

namespace soteria\secure\handlers;

use ReflectionClass;

class Handler extends AbstractHandler
{

    public function __construct($value)
    {
        parent::__construct($value);
    }

    function build($method, $arguments = [])
    {

        $className = __NAMESPACE__ .'\\'. ucfirst($method).'Handler';

        if (class_exists($className)) {

            $reflection = new ReflectionClass($className);

            if (!$reflection->isInstantiable()) {
                throw new InvalidClassException(sprintf('"%s" must be instantiable', $className));
            }

            return $reflection->newInstanceArgs($arguments);
        }
        throw new \Exception(sprintf('"%s" is not a valid handler name', $method));
    }

}