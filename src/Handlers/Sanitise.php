<?php

namespace devtoolboxuk\soteria\handlers;


use devtoolboxuk\soteria\classes\Filters;
use devtoolboxuk\soteria\classes\Strings;
use devtoolboxuk\soteria\classes\Url;
use devtoolboxuk\soteria\models\SoteriaModel;

class Sanitise
{

    private $is_valid = null;
    private $_sanitised = null;
    private $filters;
    private $input;
    private $output;
    private $strings;
    private $urlService;

    function __construct()
    {
        $this->filters = new Filters();
        $this->strings = new Strings();
        $this->urlService = new Url();
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

        $this->input = $data = trim($data);
        $data = $this->urlService->remove($data);
        $data = trim($data);

        if ($this->input != $data) {
            $this->_sanitised = true;
        }
        $this->is_valid = true;

        $this->output = $data;
        return $data;

    }

    /**
     * @param $data
     * @param string $toEncoding
     * @param string $fromEncoding
     * @return array|false|string|string[]|null
     */
    public function cleanse($data, $toEncoding = 'utf-8', $fromEncoding = 'auto')
    {

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->cleanse($value, $toEncoding, $fromEncoding);
            }
            return $data;
        }

        $this->input = $data = trim($data);
        $data = $this->strings->clean($data);
        $data = mb_convert_encoding($data, $toEncoding, $fromEncoding);
        $data = htmlspecialchars_decode($data);
        $data = $this->strings->clean($data);
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

        $this->input = $data = trim($data);

        $data = $this->strings->clean($data);
        $data = $this->strings->stringLength($data, $stringLength);

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
                $filterResult = $this->filters->filterInt($data);
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
        return $this->decodeHtmlEntity($this->output);
    }


    /**
     * @param $str
     * @return mixed|string
     */
    public function decodeHtmlEntity($str)
    {
        $ret = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        $p2 = -1;
        for (; ;) {
            $p = strpos($ret, '&#', $p2 + 1);
            if ($p === false) {
                break;
            }
            $p2 = strpos($ret, ';', $p);
            if ($p2 === false) {
                break;
            }

            if (substr($ret, $p + 2, 1) == 'x') {
                $char = hexdec(substr($ret, $p + 3, $p2 - $p - 3));
            } else {
                $char = intval(substr($ret, $p + 2, $p2 - $p - 2));
            }

            $newchar = iconv(
                'UCS-4', 'UTF-8',
                chr(($char >> 24) & 0xFF) . chr(($char >> 16) & 0xFF) . chr(($char >> 8) & 0xFF) . chr($char & 0xFF)
            );

            $ret = substr_replace($ret, $newchar, $p, 1 + $p2 - $p);
            $p2 = $p + strlen($newchar);
        }
        return $ret;
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