<?php

namespace devtoolboxuk\soteria\handlers;

use devtoolboxuk\soteria\Wrappers\StripHtmlWrapper;

class HtmlHandler extends Handler
{
    public function __construct($value = '')
    {
        parent::__construct($value);
        $this->pushWrapper(new StripHtmlWrapper());
    }
}