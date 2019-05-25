<?php

namespace devtoolboxuk\soteria\models;

class FilterModel
{
    private $result;
    private $valid = true;

    /**
     * FilterModel constructor.
     * @param $result
     * @param bool $valid
     */
    function __construct($result, $valid = true)
    {
        $this->result = $result;
        $this->valid = $valid;
    }


    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

}