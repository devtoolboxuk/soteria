<?php

namespace devtoolboxuk\soteria;

use devtoolboxuk\soteria\handlers\XssHandler;

use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    private $soteria;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->soteria = new SoteriaService();
    }

    function integrationChecks()
    {
        $string = '\x3cscript src=http://www.example.com/malicious-code.js\x3e\x3c/script\x3e';
        $string = '<li style="list-style-image: url(javascript:alert(0))">';
        $string = "Hello, i try to <script>alert('Hack');</script> your site";
        $string = "<a href=\"\u0001java\u0003script:alert(1)\">CLICK</a>";

        $string = '<iframe width="560" onclick="alert(\'xss\')" height="315" src="https://www.youtube.com/embed/foobar?rel=0&controls=0&showinfo=0" frameborder="0" allowfullscreen></iframe>';

        echo "\n";
        echo "#########################################################\n";
        echo "Original String: " . $string . "\n";
        echo "Fixed String: ";

        $data = $this->soteria->pushHandler(new XssHandler($string, ['html' => ['iframe']]));

//        $data = $this->soteria->pushHandler(new HtmlHandler($data->getResult()));
        print_r($data->getResult());
        echo "\n";
        echo "#########################################################\n";

    }
}
