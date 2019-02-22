<?php

namespace soteria\secure\handlers;

use soteria\secure\Wrappers\AttributeWrapper;
use soteria\secure\Wrappers\HtmlWrapper;
use soteria\secure\Wrappers\JavaScriptWrapper;
use soteria\secure\Wrappers\UtfWrapper;
use soteria\secure\Wrappers\XssFinalise;
use soteria\secure\Wrappers\XssInitialise;

class XssHandler extends Handler
{
    public function __construct($value = '',array $passArray = [])
    {
        parent::__construct($value);
        $this->setPassArray($passArray);
        $this->pushWrapper(new XssInitialise());
        $this->pushWrapper(new UtfWrapper());
        $this->pushWrapper(new JavaScriptWrapper());
        $this->pushWrapper(new AttributeWrapper());
        $this->pushWrapper(new HtmlWrapper());
        $this->pushWrapper(new XssFinalise());
    }
}