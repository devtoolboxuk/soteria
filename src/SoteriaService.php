<?php

namespace devtoolboxuk\soteria;

use devtoolboxuk\soteria\handlers\XssClean;


class SoteriaService //implements SoteriaInterface
{

    protected $factory;

    public function __call($method, $arguments)
    {
        return $this->buildSecurity($method);
    }

    public function buildSecurity($ruleSpec)
    {
        try {
            return $this->getFactory()->build($ruleSpec);
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }
    }

    protected function getFactory()
    {
        if (!$this->factory instanceof SoteriaFactory) {
            $this->factory = new SoteriaFactory();
        }
        return $this->factory;
    }
}