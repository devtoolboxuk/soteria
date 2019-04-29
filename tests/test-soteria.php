<?php

namespace devtoolboxuk\soteria;

//use devtoolboxuk\soteria\handlers\HtmlHandler;
//use devtoolboxuk\soteria\handlers\XssHandler;
//use devtoolboxuk\soteria\SoteriaService;
//use soteria\secure\classes\Secure;
//use soteria\secure\handlers\HtmlHandler;
//use soteria\secure\handlers\XssHandler;
//use soteria\secure\Security as s;

use devtoolboxuk\soteria\handlers\HtmlHandler;
use devtoolboxuk\soteria\handlers\XssHandler;
use devtoolboxuk\soteria\SoteriaService;
use PHPUnit\Framework\TestCase;

class SoteriaTest extends TestCase
{
    private $soteria;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->soteria = new SoteriaService();

    }

    function testXss()
    {

        $string = '<a href=\'&#x2000;javascript:alert(1)\'>CLICK</a>';
        $result = $this->soteria->pushHandler(new XssHandler($string))->getResult();
        $this->assertEquals('<a >CLICK</a>', $result);


        $string = '<a href=\"\u0001java\u0003script:alert(1)\">CLICK</a>';
        $result = $this->soteria->pushHandler(new XssHandler($string))->getResult();
        $this->assertEquals('<a >CLICK</a>',$result);

        $string = "Hello, i try to <script>alert('Hack');</script> your site";
        $result = $this->soteria->pushHandler(new XssHandler($string))->getResult();
        $this->assertEquals('Hello, i try to alert&#40;\'Hack\'&#41;; your site', $result);

        $string = "<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>";

        $result = $this->soteria->pushHandler(new XssHandler($string))->getResult();
        $this->assertEquals('<IMG >', $result);


        $string = "<a href=\"\u0001java\u0003script:alert(1)\">CLICK</a>";
        $result = $this->soteria->pushHandler(new XssHandler($string))->getResult();
        $this->assertEquals('<a >CLICK</a>', $result);

    }

}
