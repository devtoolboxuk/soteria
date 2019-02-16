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

    function testXss()
    {
        $string = '<a href=\'&#x2000;javascript:alert(1)\'>CLICK</a>';

        $this->assertEquals('CLICK', s::secure()->xss($string));


        $string = '<a href=\"\u0001java\u0003script:alert(1)\">CLICK</a>';

        $this->assertEquals('CLICK', s::secure()->xss($string));

        $string = "Hello, i try to <script>alert('Hack');</script> your site";
        $this->assertEquals('Hello, i try to alert&#40;\'Hack\'&#41;; your site', s::secure()->xss($string));

        $string = "<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>";
        $this->assertEquals('', s::secure()->xss($string));


        $string = "<a href=\"\u0001java\u0003script:alert(1)\">CLICK</a>";
        $this->assertEquals('CLICK', s::secure()->xss($string));

    }
//
//    function testCheck()
//    {
//        $string = '\x3cscript src=http://www.example.com/malicious-code.js\x3e\x3c/script\x3e';
//        $string = '<li style="list-style-image: url(javascript:alert(0))">';
//        $string = "Hello, i try to <script>alert('Hack');</script> your site";
//        $string = "<a href=\"\u0001java\u0003script:alert(1)\">CLICK</a>";
//
//
//        //   $string = 'Hello, i try to <script>alert(\'Hack\');</script> your site';
//        echo "\n";
//        echo "#########################################################\n";
//        echo "Original String: " . $string . "\n";
//        echo "Fixed String: ";
//        $result = s::secure()->xss($string);
//
//        print_r($result);
//        echo "\n";
//        echo "#########################################################\n";
//
//
//    }
}
