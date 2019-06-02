<?php

namespace devtoolboxuk\soteria\classes;

use devtoolboxuk\utilitybundle\UtilityService;

class Strings
{

    private $arrayUtility;

    private $options = [
        'stripslashes' => 1,
        'trim' => 1,
        'trimControl' => 1,
        'striptags' => 1,
    ];

    public function __construct()
    {
        $utility = new UtilityService();
        $this->arrayUtility = $utility->arrays();
    }

    /**
     * @param array $options
     */
    public function setOptions($options = [])
    {
        $this->options = $this->arrayUtility->arrayMergeRecursiveDistinct($this->options, $options);
    }

    /**
     * @param $data
     * @param int $length
     * @return bool|string
     */
    public function stringLength($data, $length = -1)
    {
        if ($length > 0) {
            if (mb_strlen($data) > $length) {
                $data = substr($data, 0, $length);
            }
        }
        return $data;
    }

    /**
     * @param $string
     * @return string
     */
    public function clean($string)
    {

        $string = implode("", explode("\\", $string));

        if ($this->getOption('stripslashes') == 1) {
            $string = stripslashes($string);
        }



        if ($this->getOption('trim') == 1) {
            $string = trim($string);
        }

        if ($this->getOption('trimControl') == 1) {
            $characters    = "[[:cntrl:]]";
            $string = trim( preg_replace( "/".$characters."]+/" , '' , $string ) , $characters );
        }


        if ($this->getOption('striptags') == 1) {
            $string = strip_tags($string);
        }

        return $string;
    }


    /**
     * @param $name
     * @return mixed|null
     */
    private function getOption($name)
    {
        if (!$this->hasOption($name)) {
            return null;
        }

        return $this->options[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    private function hasOption($name)
    {
        return isset($this->options[$name]);
    }
}