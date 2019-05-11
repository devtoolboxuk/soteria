<?php

namespace devtoolboxuk\soteria\voku\Resources;

use devtoolboxuk\soteria\handlers\XssClean;

class Decode
{


    public function string($str)
    {
        // init
        $regExForHtmlTags = '/<\p{L}+.*+/us';

        if (\strpos($str, '<') !== false && \preg_match($regExForHtmlTags, $str, $matches) === 1) {
            $str = (string)\preg_replace_callback(
                $regExForHtmlTags,
                function ($matches) {
                    return $this->entity($matches);
                },
                $str
            );
        }
//        else {
//            $str = UTF8::rawurldecode($str);
//        }

        return $str;
    }

    private function entity(array $match)
    {
        // init
        $str = $match[0];

        // protect GET variables without XSS in URLs
        if (\preg_match_all("/[\?|&]?[\\p{L}0-9_\-\[\]]+\s*=\s*(?<wrapped>\"|\042|'|\047)(?<attr>[^\\1]*?)\\g{wrapped}/ui", $str, $matches)) {
            if (isset($matches['attr'])) {
                foreach ($matches['attr'] as $matchInner) {
                    $tmpAntiXss = clone $this;

                    $urlPartClean = $tmpAntiXss->xss_clean($matchInner);

                    if ($tmpAntiXss->isXssFound() === true) {
                        $this->_xss_found = true;
                        $str = $urlPartClean;
//                        $str = \str_replace($matchInner, UTF8::rawurldecode($urlPartClean), $str);
                    }
                }
            }
        }
//        else {
//            $str = $this->_entity_decode(UTF8::rawurldecode($str));
//        }

        return $str;
    }

}