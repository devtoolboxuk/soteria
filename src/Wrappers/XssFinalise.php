<?php

namespace soteria\secure\Wrappers;


use soteria\secure\Wrappers\Resources\StringArray;

class XssFinalise extends Wrapper
{

    private $name;

    function getWrapperName()
    {
        $this->name = str_replace(__NAMESPACE__ . "\\", "", __CLASS__);
        return $this->name;
    }

    public function process()
    {
        $this->stringReplace();

        $this->cleanString();
    }

    private function cleanString()
    {
        $value = $this->getValue();
        $value = strip_tags($value);
        $this->setValue($value);
    }

    private function stringReplace()
    {
        $value = $this->getValue();
        $stringArray = new StringArray();
        foreach ($stringArray->getData() as $string) {
            $value = str_ireplace(
                $string,
                $this->getReplacementValue(),
                $value
            );
        }

        $this->setValue($value);
    }


}