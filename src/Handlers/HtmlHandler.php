<?php

namespace soteria\secure\handlers;

use soteria\secure\Wrappers\StripHtmlWrapper;

class HtmlHandler extends Handler
{
    public function __construct($value = '')
    {
        parent::__construct($value);
        $this->pushWrapper(new StripHtmlWrapper());
    }
}