<?php

namespace devtoolboxuk\soteria\handlers;

use devtoolboxuk\soteria\models\SoteriaModel;

use devtoolboxuk\soteriautf\Resources\Attributes;
use devtoolboxuk\soteriautf\Resources\Exploded;
use devtoolboxuk\soteriautf\Resources\Html;
use devtoolboxuk\soteriautf\Resources\JavaScript;
use devtoolboxuk\soteriautf\Resources\NeverAllowed;
use devtoolboxuk\soteriautf\Resources\System;
use devtoolboxuk\soteriautf\Resources\StringResource;

use devtoolboxuk\soteriautf\Utf8;
use devtoolboxuk\soteriautf\Utf7;


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
    private $system;
    private $input;
    private $output;


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

        $this->system = new System();

        $this->utf7 = new Utf7();
        $this->utf8 = new Utf8();

        $this->strings = new StringResource();
    }


    function setString($str)
    {
        $this->string = $str;
    }

    public function cleanArray($array)
    {
        return $this->clean($array);
    }

    /**
     * @param $str
     * @return array|mixed
     */
    public function clean($str)
    {
        // reset
        $this->_xss_found = null;

        // check for an array of strings
        if (\is_array($str)) {
            foreach ($str as $key => $value) {
                $str[$key] = $this->clean($value);
            }
            return $str;
        }

        $this->input = $str;
        $this->output = $this->process($str);
        return $this->output;
    }

    private function process($str)
    {

        // process
        do {
            $old_str = $str;
            $str = $this->_do($str);
        } while ($old_str !== $str);

        // keep the old value, if there wasn't any XSS attack
        if ($this->_xss_found !== true) {
            $str = $this->input;
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
        if (!$str || (string)$strInt === $str || (string)$strFloat === $str) {

            // no xss found
            if ($this->_xss_found !== true) {
                $this->_xss_found = false;
            }

            return $str;
        }

        // remove the BOM from UTF-8 / UTF-16 / UTF-32 strings
        $str = $this->utf8->remove_bom($str);

        // replace the diamond question mark (�) and invalid-UTF8 chars
        $str = $this->utf8->replace_diamond_question_mark($str, '');

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

        if (strpos($str, '<') !== false && preg_match($regExForHtmlTags, $str, $matches) === 1) {
            $str = (string)preg_replace_callback(
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
        if (preg_match_all("/[\?|&]?[\\p{L}0-9_\-\[\]]+\s*=\s*(?<wrapped>\"|\042|'|\047)(?<attr>[^\\1]*?)\\g{wrapped}/ui", $str, $matches)) {
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
        if (preg_match_all('/(?<html_entity>&[A-Za-z]{2,}[;]{0})/', $str, $matches)) {
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
        return include __DIR__ . '/../vendor/devtoolboxuk/soteriautf/src/Data/' . $file . '.php';
    }

    public function cleanString($str)
    {
        return $this->clean($str);
    }

    /**
     * @param $str
     * @return array|mixed
     */
    public function cleanUrl($str)
    {
        $this->input = $str;
        $str = $this->clean($str);

        if (is_numeric($str) || is_null($str)) {
            return $str;
        }

        if (is_array($str)) {
            foreach ($str as $key => $value) {
                $str[$key] = $this->cleanUrl($value);
            }
            return $str;
        }

        do {
            $decode_str = rawurldecode($str);
            $str = $this->_do($str);
        } while ($decode_str !== $str);

        $this->output = $str;

        return $str;
    }

    function result()
    {
        $valid = false;
        if (!$this->_xss_found) {
            $valid = true;
        }
        return new SoteriaModel($this->input, $this->output, $valid);
    }
}