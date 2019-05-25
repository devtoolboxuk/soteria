<?php

namespace devtoolboxuk\soteria\handlers;


use devtoolboxuk\soteria\classes\Filters;
use devtoolboxuk\soteria\models\SoteriaModel;

class Sanitise
{

    private $is_valid = null;
    private $_sanitised = null;
    private $filters;
    private $input;
    private $output;

    private $urlRegEx = '/\b((https?|ftp|file):\/\/|www\.)[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';

    function __construct()
    {
        $this->filters = new Filters();
    }

    /**
     *
     * Removes URLs from strings
     *
     * @param array|string $data
     * @return array|string|string[]|null
     */
    public function removeUrl($data)
    {
        $this->_sanitised = null;

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->removeUrl($value);
            }
            return $data;
        }

        $this->input = $data;

        $data = $this->cleanString($data);
        $data = preg_replace($this->urlRegEx, ' ', $data);

        if ($this->input != $data) {
            $this->_sanitised = true;
        }
        $this->is_valid = true;

        $this->output = $data;
        return $data;

    }

    /**
     * @param $data
     * @return string
     */
    private function cleanString($data)
    {
        $data = implode("", explode("\\", $data));
        return strip_tags(trim(stripslashes($data)));
    }

    /**
     * @param $data
     * @return array|false|string|string[]|null
     */
    public function cleanse($data)
    {

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->cleanse($value);
            }
            return $data;
        }

        $this->input = $data;
        $data = $this->cleanString($data);
        $data = mb_convert_encoding($data, "utf-8", "auto");
        $data = htmlspecialchars_decode($data);
        $data = $this->cleanString($data);
        if ($this->input != $data) {
            $this->_sanitised = true;
        }
        $this->output = $data;
        return $data;
    }

    /**
     * @param $string
     * @param string $delimiter
     * @return string
     */
    public function cleanseCsv($string, $delimiter = "|")
    {
        return trim(str_replace([$delimiter, "\n", "\r", "\t"], " ", $string));
    }

    /**
     * @param $data
     * @param string $type
     * @param int $stringLength
     * @return mixed|string
     */
    public function disinfect($data, $type = 'special_chars', $stringLength = -1)
    {

        $this->_sanitised = null;

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->disinfect($value, $type, $stringLength);
            }
            return $data;
        }

        $this->input = $data;

        $data = $this->cleanString($data);
        $data = $this->stringLength($data, $stringLength);

        switch ($type) {
            case "email":
                $filterResult = $this->filters->filterEmail($data);
                break;

            case "encoded":
                $filterResult = $this->filters->filterEncoded($data);
                break;

            case "number_float":
            case "float":
                $filterResult = $this->filters->filterFloat($data);
                break;

            case "number_int":
            case "int":
                $filterResult = $this->filters->filtetInt($data);
                break;

            case "full_special_chars":
                $filterResult = $this->filters->filterFullSpecialChar($data);
                break;

            case "url":
                $filterResult = $this->filters->filterUrl($data);
                break;

            case "string":
                $filterResult = $this->filters->filterString($data);
                break;

            default:
            case "special_chars":
                $filterResult = $this->filters->filterSpecial($data);
                break;
        }

        if ($this->input != $filterResult->getResult()) {
            $this->_sanitised = true;
        }

        $this->is_valid = $filterResult->isValid();
        $this->output = $filterResult->getResult();
        return $this->output;
    }

    /**
     * @param $data
     * @param int $length
     * @return bool|string
     */
    private function stringLength($data, $length = -1)
    {
        if ($length > 0) {
            if (mb_strlen($data) > $length) {
                $data = substr($data, 0, $length);
            }
        }
        return $data;
    }


    /**
     * @return null
     */
    public function isSanitised()
    {
        return $this->_sanitised;
    }

    /**
     * Returns true if the data is valid
     * @return null
     */
    public function isValid()
    {
        return $this->is_valid;
    }

    function result()
    {
        $valid = false;
        if (!$this->_sanitised && $this->is_valid) {
            $valid = true;
        }
        return new SoteriaModel($this->input, $this->output, $valid);
    }

}