<?php

namespace soteria\secure\Wrappers;

class StripHtmlWrapper extends Wrapper
{

    private $name;

    function getWrapperName()
    {
        $this->name = str_replace(__NAMESPACE__ . "\\", "", __CLASS__);
        return $this->name;
    }

    public function process()
    {
        $value = $this->getValue();
        $value = strip_tags($value);
        $this->setValue($value);
    }


}