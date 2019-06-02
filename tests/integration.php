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
        $xss = $this->security->xss();
        $sanitise = $this->security->sanitise();


        echo "\nXSS";
        $unCleanString = 'Visit my website http://www.doajob.org?redirect=https://www.google.com';



        echo "\nUnclean String: " . $unCleanString;
        $cleanString = $xss->clean($unCleanString);
        echo "\nXSS Cleaned String: " . $cleanString;
        $cleanString = $xss->cleanUrl($unCleanString);
        echo "\nXSS Cleaned Url: " . $cleanString;
        echo "\n";

        echo "\nSanitised Url: " . $sanitise->removeUrl($unCleanString);
        if ($sanitise->isSanitised()) {
            echo "\n1";
        }

        echo "\nString without a Url: " . $sanitise->removeUrl("Rob WIlson");
        if ($sanitise->isSanitised()) {
            echo "\n1";
        }


    }

}
