<?php

namespace devtoolboxuk\soteria\classes;

use devtoolboxuk\soteria\models\FilterModel;

class Filters
{

    /**
     * @param $data
     * @return FilterModel
     */
    public function filterEmail($data)
    {
        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_EMAIL),
            filter_var($data, FILTER_VALIDATE_EMAIL)
        );
    }

    /**
     * @param $data
     * @return FilterModel
     */
    public function filterEncoded($data)
    {
        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_ENCODED)
        );
    }

    /**
     * @param $data
     * @return FilterModel
     */
    public function filterFloat($data)
    {
        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT),
            filter_var($data, FILTER_VALIDATE_FLOAT)
        );
    }

    /**
     * @param $data
     * @return FilterModel
     */
    public function filterFloatFraction($data)
    {
        $result = filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return $result;
    }

    /**
     * @param $data
     * @return FilterModel
     */
    public function filterInt($data)
    {
        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_NUMBER_INT),
            filter_var((int)$data, FILTER_VALIDATE_INT)
        );

    }

    /**
     * @param $data
     * @return FilterModel
     */
    public function filterFullSpecialChar($data)
    {

        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES)
        );
    }

    /**
     * @param $data
     * @return FilterModel
     */
    public function filterUrl($data)
    {

        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_URL),
            filter_var($data, FILTER_VALIDATE_URL)
        );
    }

    /**
     * @param $data
     * @return FilterModel
     */
    public function filterString($data)
    {
        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)
        );

    }

    /**
     * @param $data
     * @return FilterModel
     */
    public function filterSpecial($data)
    {

        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS)
        );
    }

}