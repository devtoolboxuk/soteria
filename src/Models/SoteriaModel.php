<?php

namespace devtoolboxuk\soteria\models;

class SoteriaModel
{
    private $input = '';
    private $output = '';
    private $passed = true;


    function __construct($input, $output, $passed = true)
    {
        $this->input = $input;
        $this->output = $output;
        $this->passed = $passed;
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->getOutput();
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->passed;
    }

}