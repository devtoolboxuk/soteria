<?php

namespace devtoolboxuk\soteria\handlers;

use devtoolboxuk\sanitiser\SanitiserService;

class Filter
{

    private $input = null;
    private $is_valid = null;
    private $output = null;
    private $sanitiser;

    function __construct()
    {
        $this->sanitiser = new SanitiserService();
    }


    function __call($type, $arguments)
    {
        $this->input = $this->output = null;
        return $this->build($type, $arguments);
    }

    /**
     * @param string $type
     * @param array $arguments
     * @return bool|mixed
     */
    public function build($type, $arguments)
    {
        $input = $arguments[0];
        $output = $this->sanitiser->disinfect($input, $type);

        $this->validate($input, $type);

        if ($this->is_valid) {
            return $output;
        }
        return false;
    }

    /**
     * @param string $data
     * @param string $type
     */
    function validate($data, $type)
    {
        $this->is_valid = true;

        switch ($type) {
            case "email":
                $this->is_valid = filter_var($data, FILTER_VALIDATE_EMAIL);
                break;

            case "number_int":
            case "int":
                $this->is_valid = filter_var((int)$data, FILTER_VALIDATE_INT);
                break;


            case "number_float":
            case "float":
                $this->is_valid = filter_var($data, FILTER_VALIDATE_FLOAT);
                break;

            case "url":
                $this->is_valid = filter_var($data, FILTER_VALIDATE_URL);
                break;

            default:
                break;
        }
    }

    /**
     * @return null
     */
    public function isValid()
    {
        return $this->is_valid;
    }


}