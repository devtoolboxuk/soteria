<?php

namespace devtoolboxuk\soteria\handlers;

use devtoolboxuk\soteria\voku\Resources\Attributes;

use devtoolboxuk\soteria\voku\Resources\Exploded;
use devtoolboxuk\soteria\voku\Resources\Html;
use devtoolboxuk\soteria\voku\Resources\JavaScript;
use devtoolboxuk\soteria\voku\Resources\NeverAllowed;

use devtoolboxuk\soteria\voku\Resources\Utf7;
use devtoolboxuk\soteria\voku\Resources\Utf8;
use devtoolboxuk\soteria\voku\Resources\StringResource;


class Xss
{

    private $_xss_found = null;
    private $neverAllowed;
    private $exploded;
    private $string;
    private $attributes;
    private $javascript;
    private $html;
    private $utf7;
    private $utf8;
    private $strings;


    function __construct()
    {
        $this->init();
    }

    function init()
    {
        $this->neverAllowed = new NeverAllowed();

        $this->exploded = new Exploded();

        $this->attributes = new Attributes();
        $this->javascript = new JavaScript();
        $this->html = new Html();


       $this->utf7 = new Utf7();
      //  if ($this->isCompatible()) {
            $this->utf8 = new Utf8();
       // }
        $this->strings = new StringResource();
    }

    public function isCompatible()
    {
        if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
            return true;
        }
        return false;
    }

    function setString($str)
    {
        $this->string = $str;
    }

    public function cleanArray($array)
    {
        return $this->clean($array);
    }

    public function cleanString($str)
    {
        return $this->clean($str);
    }

    private function process($str, $old_str_backup)
    {

        // process
        do {
            $old_str = $str;
            $str = $this->_do($str);
        } while ($old_str !== $str);

        // keep the old value, if there wasn't any XSS attack
        if ($this->_xss_found !== true) {
            $str = $old_str_backup;
        }

        return $str;
    }

    /**
     * @param StringResource $str
     *
     * @return mixed
     */
    private function _do($str)
    {
        $str = (string)$str;
        $strInt = (int)$str;
        $strFloat = (float)$str;
        if (
            !$str
            ||
            (string)$strInt === $str
            ||
            (string)$strFloat === $str
        ) {

            // no xss found
            if ($this->_xss_found !== true) {
                $this->_xss_found = false;
            }

            return $str;
        }


        // remove the BOM from UTF-8 / UTF-16 / UTF-32 strings
        $str = $this->utf8->remove_bom($str);

        // replace the diamond question mark (�) and invalid-UTF8 chars
      //  if ($this->isCompatible()) {
            $str = $this->utf8->replace_diamond_question_mark($str, '');
      //  } else {
      //      $str = $this->utf8->replace_diamond_question_mark($str, '',false);
      //  }

        // replace invisible characters with one single space
        $str = $this->utf8->remove_invisible_characters($str, true, ' ');

         $str = $this->utf8->normalize_whitespace($str);

        $str = $this->strings->replace($str);

        // decode UTF-7 characters
        $str = $this->utf7->repack($str);


        // decode the string
        $str = $this->decodeString($str); // RW Partly DONE

        // backup the string (for later comparision)
        $str_backup = $str;

        // remove strings that are never allowed
        $str = $this->neverAllowed->doNeverAllowed($str); //RW DONE

        // corrects words before the browser will do it
        $str = $this->exploded->compactExplodedString($str); //RW DONE

        // remove disallowed javascript calls in links, images etc.
        $str = $this->javascript->removeDisallowedJavascript($str);

        // remove evil attributes such as style, onclick and xmlns
        $str = $this->attributes->removeEvilAttributes($str);

        // sanitize naughty JavaScript elements
        $str = $this->javascript->naughtyJavascript($str);

        // sanitize naughty HTML elements
        $str = $this->html->naughtyHtml($str);

        // final clean up
        //
        // -> This adds a bit of extra precaution in case something got through the above filters.
        $str = $this->neverAllowed->doNeverAllowedAfterwards($str);

        // check for xss
        if ($this->_xss_found !== true) {
            $this->_xss_found = !($str_backup === $str);
        }

        return $str;
    }

    public function decodeString($str)
    {
        // init
        $regExForHtmlTags = '/<\p{L}+.*+/us';

        if (\strpos($str, '<') !== false && \preg_match($regExForHtmlTags, $str, $matches) === 1) {
            $str = (string)\preg_replace_callback(
                $regExForHtmlTags,
                function ($matches) {
                    return $this->decodeEntity($matches);
                },
                $str
            );
        } else {
            $str = $this->utf8->rawurldecode($str);
        }

        return $str;
    }

    private function decodeEntity(array $match)
    {
        // init
        $str = $match[0];

        // protect GET variables without XSS in URLs
        if (\preg_match_all("/[\?|&]?[\\p{L}0-9_\-\[\]]+\s*=\s*(?<wrapped>\"|\042|'|\047)(?<attr>[^\\1]*?)\\g{wrapped}/ui", $str, $matches)) {
            if (isset($matches['attr'])) {
                foreach ($matches['attr'] as $matchInner) {
                    $tmpAntiXss = clone $this;
                    $urlPartClean = $tmpAntiXss->clean($matchInner);

                    if ($tmpAntiXss->isXssFound() === true) {
                        $this->_xss_found = true;
                        $str = \str_replace($matchInner, $this->utf8->rawurldecode($urlPartClean), $str);
                    }
                }
            }
        } else {
            $str = $this->_entity_decode($this->utf8->rawurldecode($str));
        }

        return $str;
    }

    /**
     * @param $str
     * @return array|mixed
     */
    public function clean($str)
    {
        // reset
        $this->_xss_found = null;

        if ($this->isCompatible()) {
            // check for an array of strings
            if (\is_array($str)) {
                foreach ($str as $key => &$value) {
                    $str[$key] = $this->clean($value);
                }
                return $str;
            }
        }

        $old_str_backup = $str;

        return $this->process($str, $old_str_backup);
    }

    /**
     * @param $str
     * @return array|mixed
     */
    public function cleanUrl($str)
    {
        $str = $this->clean($str);

        if (is_numeric($str) || is_null($str)) {
            return $str;
        }

        if ($this->isCompatible()) {
            if (\is_array($str)) {
                foreach ($str as $key => &$value) {
                    $str[$key] = $this->cleanUrl($value);
                }
                return $str;
            }
        }

        do {
            $decode_str = rawurldecode($str);
            $str = $this->_do($str);
        } while ($decode_str !== $str);

        return $str;
    }

    /**
     * @return null
     */
    public function isXssFound()
    {
        return $this->_xss_found;
    }

    /**
     * Entity-decoding.
     *
     * @param StringResource $str
     *
     * @return StringResource
     */
    private function _entity_decode($str)
    {
        static $HTML_ENTITIES_CACHE;

        $flags = ENT_QUOTES | ENT_HTML5 | ENT_DISALLOWED | ENT_SUBSTITUTE;

        // decode
        $str = html_entity_decode($str, $flags);


        // decode-again, for e.g. HHVM or miss configured applications ...
        if (\preg_match_all('/(?<html_entity>&[A-Za-z]{2,}[;]{0})/', $str, $matches)) {
            if ($HTML_ENTITIES_CACHE === null) {

                // links:
                // - http://dev.w3.org/html5/html-author/charref
                // - http://www.w3schools.com/charsets/ref_html_entities_n.asp
                $entitiesSecurity = [
                    '&#x00000;' => '',
                    '&#0;' => '',
                    '&#x00001;' => '',
                    '&#1;' => '',
                    '&nvgt;' => '',
                    '&#61253;' => '',
                    '&#x0EF45;' => '',
                    '&shy;' => '',
                    '&#x000AD;' => '',
                    '&#173;' => '',
                    '&colon;' => ':',
                    '&#x0003A;' => ':',
                    '&#58;' => ':',
                    '&lpar;' => '(',
                    '&#x00028;' => '(',
                    '&#40;' => '(',
                    '&rpar;' => ')',
                    '&#x00029;' => ')',
                    '&#41;' => ')',
                    '&quest;' => '?',
                    '&#x0003F;' => '?',
                    '&#63;' => '?',
                    '&sol;' => '/',
                    '&#x0002F;' => '/',
                    '&#47;' => '/',
                    '&apos;' => '\'',
                    '&#x00027;' => '\'',
                    '&#039;' => '\'',
                    '&#39;' => '\'',
                    '&#x27;' => '\'',
                    '&bsol;' => '\'',
                    '&#x0005C;' => '\\',
                    '&#92;' => '\\',
                    '&comma;' => ',',
                    '&#x0002C;' => ',',
                    '&#44;' => ',',
                    '&period;' => '.',
                    '&#x0002E;' => '.',
                    '&quot;' => '"',
                    '&QUOT;' => '"',
                    '&#x00022;' => '"',
                    '&#34;' => '"',
                    '&grave;' => '`',
                    '&DiacriticalGrave;' => '`',
                    '&#x00060;' => '`',
                    '&#96;' => '`',
                    '&#46;' => '.',
                    '&equals;' => '=',
                    '&#x0003D;' => '=',
                    '&#61;' => '=',
                    '&newline;' => "\n",
                    '&#x0000A;' => "\n",
                    '&#10;' => "\n",
                    '&tab;' => "\t",
                    '&#x00009;' => "\t",
                    '&#9;' => "\t",
                ];

                $HTML_ENTITIES_CACHE = \array_merge(
                    $entitiesSecurity,
                    \array_flip(\get_html_translation_table(HTML_ENTITIES, $flags)),
                    \array_flip($this->_get_data('entities_fallback'))
                );
            }

            $search = [];
            $replace = [];
            foreach ($matches['html_entity'] as $match) {
                $match .= ';';
                if (isset($HTML_ENTITIES_CACHE[$match])) {
                    $search[$match] = $match;
                    $replace[$match] = $HTML_ENTITIES_CACHE[$match];
                }
            }

            if (\count($replace) > 0) {
                $str = \str_replace($search, $replace, $str);
            }
        }

        return $str;
    }

    private function _get_data($file)
    {
        /** @noinspection PhpIncludeInspection */
        return include __DIR__ . '/../voku/Data/' . $file . '.php';
    }
}