<?php

namespace devtoolboxuk\soteria;

use PHPUnit\Framework\TestCase;

class integration extends TestCase
{
    private $security;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->security = new SoteriaService();
    }

    function testIntegration()
    {
//        $xss = $this->security->xss();
        $sanitise = $this->security->sanitise();


        echo "\nXSS";
        $string = 'Visit my website http://www.doajob.org?redirect=https://www.google.com';
//        echo "\nString: " . $string;
//        $cleanString = $xss->clean($string);
//        echo "\nString: " . $cleanString;
//        echo "\n";
//        echo "\nXSS Url";
//        $string = 'Visit my website http://www.doajob.org?redirect=https://www.google.com';
//        echo "\nString: " . $string;
//        $cleanString = $xss->cleanUrl($string);
//        echo "\nString: " . $cleanString;
//        echo "\n";
//        echo "\nSanitiser";

        echo "\nString: " . $sanitise->removeUrl($string);
        if ($sanitise->isSanitised()) {
            echo "\n1";
        }

        echo "\nString: " . $sanitise->removeUrl("Rob WIlson");
        if ($sanitise->isSanitised()) {
            echo "\n1";
        }


    }

}
