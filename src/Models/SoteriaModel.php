<?php

namespace devtoolboxuk\soteria\Models;

class SoteriaModel
{
    /**
     * @var string
     */
    private $result = '';

    /**
     * DetectModel constructor.
     * @param string $result
     */
    function __construct($result = '')
    {
        $this->result = $result;
    }

    /**
     * @return false|string
     */
    public function getResult()
    {
        return $this->result;
    }

}