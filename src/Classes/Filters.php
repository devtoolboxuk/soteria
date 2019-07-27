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
        $valid = false;
        if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
            $valid = true;
        }

        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_EMAIL),
            $valid
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
        $valid = false;
        if (filter_var($data, FILTER_VALIDATE_FLOAT)) {
            $valid = true;
        }

        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT),
            $valid
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
        $valid = false;
        if ( filter_var((int)$data, FILTER_VALIDATE_INT)) {
            $valid = true;
        }

        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_NUMBER_INT),
            $valid
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
        $valid = false;
        if ( filter_var((int)$data, FILTER_VALIDATE_URL)) {
            $valid = true;
        }

        return new FilterModel(
            filter_var($data, FILTER_SANITIZE_URL),
            $valid
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