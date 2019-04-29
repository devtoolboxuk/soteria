<?php

namespace devtoolboxuk\soteria\handlers;

use devtoolboxuk\soteria\Wrappers\AttributeWrapper;
use devtoolboxuk\soteria\Wrappers\HtmlWrapper;
use devtoolboxuk\soteria\Wrappers\JavaScriptWrapper;
use devtoolboxuk\soteria\Wrappers\UtfWrapper;
use devtoolboxuk\soteria\Wrappers\XssFinalise;
use devtoolboxuk\soteria\Wrappers\XssInitialise;

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