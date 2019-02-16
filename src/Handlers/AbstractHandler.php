<?php

namespace soteria\secure\handlers;

abstract class AbstractHandler
{
    private $active = false;
    private $wrappers = [];
    private $value;
    private $passArray;
    private $name;

    private $score;

    public function __construct($value)
    {
        $this->setValue($value);
    }

    public function getValue()
    {
        return $this->value;
    }

    protected function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    function getPassArray()
    {
        return $this->passArray;
    }

    public function setPassArray($passArray)
    {
        $this->passArray = $passArray;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function setName($value)
    {
        $this->name = $value;
        return $this;
    }

    public function getWrappers()
    {
        return $this->wrappers;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function setScore($score)
    {
        $this->score = $score;
        return $this;
    }

    public function pushWrapper($wrapper)
    {
        array_unshift($this->wrappers, $wrapper);
        return $this;
    }

    protected function getPrefixes()
    {
        return $this->prefixes;
    }

}