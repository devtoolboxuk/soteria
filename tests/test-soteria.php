<?php

namespace soteria\secure;

use soteria\secure\Security as s;

use PHPUnit\Framework\TestCase;

class DetectionTest extends TestCase
{
    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    function testCheck()
    {
        $string = '\x3cscript src=http://www.example.com/malicious-code.js\x3e\x3c/script\x3e';

        print_r(s::secure()->xss($string));

    }
}
