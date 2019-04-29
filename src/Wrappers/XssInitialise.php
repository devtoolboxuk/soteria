<?php

namespace devtoolboxuk\soteria\Wrappers;

use devtoolboxuk\soteria\Wrappers\Resources\EntitiesArray;
use devtoolboxuk\soteria\Wrappers\Resources\RegExArray;

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

        $this->decodeString();
        $this->decodeEntity();

        $this->setValue($value);
    }

    function decodeString()
    {
        $value = $this->getValue();

        $regExArray = new RegExArray();
        foreach ($regExArray->getData() as $regEx) {
            $value = (string)preg_replace('#' . $regEx . '#is', $this->getReplacementValue(), $value);
        }

        $this->setValue($value);
    }

    private function decodeEntity()
    {
        $value = $this->getValue();
        $entitiesArray = new EntitiesArray();

        foreach ($entitiesArray->getData() as $key=>$entity) {
            $value = str_replace($key,$entity, $value);
        }

        $this->setValue($value);
    }
}