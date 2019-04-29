<?php

namespace devtoolboxuk\soteria\Wrappers;

class UtfWrapper extends Wrapper
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

        #7 bit
        $value = preg_replace('/[\x00-\x1F\x7F-\xFF]/', $this->getReplacementValue(), $value);

        #8 bit
        $value = preg_replace('/[\x00-\x1F\x7F]/', $this->getReplacementValue(), $value);

        #UTF 8
        $value = preg_replace('/[\x00-\x1F\x7F]/u', $this->getReplacementValue(), $value);


        $this->setValue($value);
    }

}