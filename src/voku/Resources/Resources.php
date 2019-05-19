<?php

namespace devtoolboxuk\soteria\voku\Resources;
class Resources
{

    protected $_replacement = '';

    protected $_spacing_regex = '(?:\s|"|\042|\'|\047|\+|&#x09;|&#x0[A-F];|%0a)*+';

    protected function getReplacementValue()
    {
        return $this->_replacement;
    }

    function setReplacementValue($data)
    {
        $this->_replacement = $data;
    }

    protected function _filter_attributes($str)
    {
        if ($str === '') {
            return '';
        }

        $out = '';
        if (preg_match_all('#\s*[\\p{L}0-9_\-\[\]]+\s*=\s*("|\042|\'|\047)(?:[^\\1]*?)\\1#ui', $str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= $match;
            }
        }

        return $out;
    }
}