<?php

namespace devtoolboxuk\soteria\Wrappers\Resources;

class JavaScriptArray
{

    private $dataArray = [
        'javascript',
        'expression',
        'ｅｘｐｒｅｓｓｉｏｎ',
        'view-source',
        'vbscript',
        'jscript',
        'wscript',
        'vbs',
        'script',
        'base64',
        'applet',
        'alert',
        'document',
        'write',
        'cookie',
        'window',
        'confirm',
        'prompt',
        'eval',
    ];

    function getData()
    {
        return $this->dataArray;
    }
}