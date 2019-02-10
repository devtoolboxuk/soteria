<?php

namespace soteria\secure\Wrappers;

abstract class Wrapper
{

    private $value;
    private $result;

    protected $replacementValue = '';

    function getResult()
    {
        return $this->result;
    }

    protected function getReplacementValue()
    {
        return $this->replacementValue;
    }

    function setReplacementValue($data)
    {
        $this->replacementValue = $data;
        return $this;
    }

    protected function setResult($data) {
        $this->result = $data;
        return $this;
    }

    function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

}