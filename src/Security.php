<?php

namespace soteria\secure;

class Security
{
    protected static $factory;

    /**
     * @return mixed
     */
    public static function create()
    {
        $ref = new ReflectionClass(__CLASS__);
        return $ref->newInstance(func_get_args());
    }

    /**
     * @param $ruleName
     * @param array $arguments
     * @return object
     */
    public static function __callStatic($ruleName, $arguments = [])
    {
        $validator = new static();
        return $validator->__call($ruleName, $arguments);
    }

    /**
     * @param $method
     * @param $arguments
     * @return object
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        return static::buildSecurity($method);
    }

    /**
     * @param $ruleSpec
     * @return object
     * @throws \Exception
     */
    public static function buildSecurity($ruleSpec)
    {
        try {
            return static::getFactory()->build($ruleSpec);
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }
    }

    /**
     * @return Factory
     */
    protected static function getFactory()
    {
        if (!static::$factory instanceof Factory) {
            static::$factory = new Factory();
        }

        return static::$factory;
    }

    /**
     * @param Factory $factory
     */
    public static function setFactory($factory)
    {
        static::$factory = $factory;
    }

}
