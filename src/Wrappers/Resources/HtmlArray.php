<?php

namespace devtoolboxuk\soteria\Wrappers\Resources;

class HtmlArray
{

    private $dataArray = [
        'applet',
        'alert',
        'audio',
        'basefont',
        'base',
        'behavior',
        'bgsound',
        'blink',
        'body',
        'embed',
        'eval',
        'expression',
        'form',
        'frameset',
        'frame',
        'head',
        'html',
        'ilayer',
        'iframe',
        'input',
        'button',
        'select',
        'isindex',
        'layer',
        'link',
        'meta',
        'keygen',
        'object',
        'plaintext',
        'style',
        'script',
        'textarea',
        'title',
        'math',
        'video',
        'source',
        'svg',
        'xml',
        'xss',
    ];


    function getData()
    {
        return $this->dataArray;
    }

}