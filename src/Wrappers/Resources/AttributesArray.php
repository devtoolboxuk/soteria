<?php

namespace soteria\secure\Wrappers\Resources;

class AttributesArray
{

    private $dataArray = [
        'on\w*',
        'style',
        'xmlns',
        'formaction',
        'form',
        'xlink:href',
        'seekSegmentTime',
        'FSCommand',
    ];

    function getData()
    {
        return $this->dataArray;
    }
}