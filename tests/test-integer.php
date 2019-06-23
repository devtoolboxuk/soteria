<?php

namespace devtoolboxuk\soteria;

use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{

    private $sanitise;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->security = new SoteriaService();
        $this->sanitise = $this->security->sanitise();
    }

    function testInteger()
    {
        $this->sanitise->disinfect(12, 'integer');

        $result = $this->sanitise->result();


        $this->assertTrue($result->isValid());
//        if ($result->isValid()) {
//            echo "\nValid";
//        }
    }

}
