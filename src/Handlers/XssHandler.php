<?php

namespace soteria\secure\handlers;

use soteria\secure\Wrappers\RegExWrapper;
use soteria\secure\Wrappers\StringWrapper;

class XssHandler extends Handler
{
    public function __construct($value = '')
    {
        parent::__construct($value);
        $this->pushWrapper(new RegExWrapper());
        $this->pushWrapper(new StringWrapper());
    }
}