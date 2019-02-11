<?php

namespace soteria\secure\Wrappers;

use soteria\secure\Wrappers\Resources\RegExArray;

class XssInitialise extends Wrapper
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
        $regExArray = new RegExArray();
        foreach ($regExArray->getData() as $regEx) {
            $value = (string)preg_replace('#' . $regEx . '#is', $this->getReplacementValue(), $value);
        }

        $this->setValue($value);
    }
}