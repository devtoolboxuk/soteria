<?php

namespace soteria\secure;

use ReflectionClass;

class Factory
{
    protected $rulePrefixes = [
        'soteria\\secure\\classes\\',
    ];

    public function build($ruleName, $options = [])
    {
        foreach ($this->getRulePrefixes() as $prefix) {

            $className = $prefix . ucfirst($ruleName);
            if (!class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            return $reflection->newInstance($options);
        }

        throw new \Exception(sprintf('"%s" is not a valid rule name', $ruleName));
    }

    public function getRulePrefixes()
    {
        return $this->rulePrefixes;
    }

}
