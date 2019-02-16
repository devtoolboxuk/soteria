<?php

namespace soteria\secure\Wrappers;

abstract class Wrapper
{

    protected $replacementValue = '';
    private $value;
    private $result;
    private $passArray = [];

    function getResult()
    {
        return $this->result;
    }

    protected function setResult($data)
    {
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

    function getArrayData($array, $type = '')
    {
        return array_diff(
            $array,
            array_intersect($this->getPassArray($type), $array)
        );
    }

    private function getPassArray($type = '')
    {

        if (isset($this->passArray[$type])) {
            return $this->passArray[$type];
        }

        return [];
    }

    public function setPassArray($passArray)
    {
        $this->passArray = $passArray;
        return $this;
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

}