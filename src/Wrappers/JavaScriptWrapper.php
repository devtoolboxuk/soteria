<?php

namespace soteria\secure\Wrappers;

use soteria\secure\Wrappers\Resources\JavaScriptArray;

class JavaScriptWrapper extends Wrapper
{

    private $wordCache = [];

    private $name;

    function getWrapperName()
    {
        $this->name = str_replace(__NAMESPACE__ . "\\", "", __CLASS__);
        return $this->name;
    }

    public function process()
    {
        $value = $this->getValue();

        $value = $this->explodedJavaScript($value);
        $value = $this->removeDisallowedJavaScript($value);
        $value = $this->sanitizeJavascript($value);

        $this->setValue($value);
    }

    private function explodedJavaScript($value)
    {
        $javaScriptArray = new JavaScriptArray();
        foreach ($javaScriptArray->getData() as $word) {
            if (!isset($this->wordCache[$word])) {
                $regex = '(?:\s|\+|"|\042|\'|\047)*';
                $word = $this->wordCache[$word] = substr(
                    chunk_split($word, 1, $regex),
                    0,
                    -strlen($regex)
                );
            } else {
                $value = $this->wordCache[$word];
            }
            // We only want to do this when it is followed by a non-word character
            // That way valid stuff like "dealer to" does not become "dealerto".
            $value = (string)preg_replace_callback(
                '#(?<word>' . $word . ')(?<rest>\W)#is',
                [
                    $this,
                    'explodedWordsCallback',
                ],
                $value
            );
        }
        return $value;
    }

    private function removeDisallowedJavascript($value)
    {
        do {
            $original = $value;
            if (stripos($value, '<a') !== false) {
                $value = (string)preg_replace_callback(
                    '#<a[^a-z0-9>]+([^>]*?)(?:>|$)#i',
                    [
                        $this,
                        'linkRemovalCallback',
                    ],
                    $value
                );
            }
            if (stripos($value, '<img') !== false) {
                $value = (string)preg_replace_callback(
                    '#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#i',
                    [
                        $this,
                        'srcRemovalCallback',
                    ],
                    $value
                );
            }
            if (stripos($value, '<audio') !== false) {
                $value = (string)preg_replace_callback(
                    '#<audio[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#i',
                    [
                        $this,
                        'srcRemovalCallback',
                    ],
                    $value
                );
            }
            if (stripos($value, '<video') !== false) {
                $value = (string)preg_replace_callback(
                    '#<video[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#i',
                    [
                        $this,
                        'srcRemovalCallback',
                    ],
                    $value
                );
            }
            if (stripos($value, '<source') !== false) {
                $value = (string)preg_replace_callback(
                    '#<source[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#i',
                    [
                        $this,
                        'srcRemovalCallback',
                    ],
                    $value
                );
            }
            if (stripos($value, 'script') !== false) {
                // US-ASCII: ¼ === <
                $value = (string)preg_replace(
                    '#(?:¼|<)/*(?:script).*(?:¾|>)#isuU',
                    $this->getReplacementValue(),
                    $value
                );
            }
        } while ($original !== $value);
        return (string)$value;
    }

    private function sanitizeJavascript($value)
    {
        $value = (string)preg_replace(
            '#(alert|eval|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*)\)#siU',
            '\\1\\2&#40;\\3&#41;',
            $value
        );
        return (string)$value;
    }

    private function linkRemovalCallback(array $match)
    {
        return $this->removalCallback($match, 'href');
    }

    private function removalCallback(array $match, string $search)
    {
        if (!$match[0]) {
            return '';
        }
        // init
        $match_style_matched = false;
        $match_style = [];
        // hack for style attributes v1
        if ($search === 'href') {
            \preg_match('/style=".*?"/i', $match[0], $match_style);
            $match_style_matched = (\count($match_style) > 0);
            if ($match_style_matched === true) {
                $match[0] = \str_replace($match_style[0], 'xss::STYLE', $match[0]);
            }
        }

        $replacer = $this->filterAttributes(\str_replace(['<', '>'], '', $match[1]));
        $pattern = '#' . $search . '=.*(?:\(.+([^\)]*?)(?:\)|$)|javascript:|view-source:|livescript:|wscript:|vbscript:|mocha:|charset=|window\.|\(?document\)?\.|\.cookie|<script|d\s*a\s*t\s*a\s*:)#is';
        $matchInner = [];

        preg_match($pattern, $match[1], $matchInner);
        if (\count($matchInner) > 0) {
            $replacer = (string)\preg_replace(
                $pattern,
                $search . '="' . $this->getReplacementValue() . '"',
                $replacer
            );
        }

        $return = str_ireplace($match[1], $replacer, (string)$match[0]);
        // hack for style attributes v2
        if (
            $match_style_matched === true
            &&
            $search === 'href'
        ) {
            $return = str_replace('xss::STYLE', $match_style[0], $return);
        }
        return $return;
    }

    private function filterAttributes(string $value)
    {
        if ($value === '') {
            return '';
        }
        $out = '';
        if (preg_match_all('#\s*[\\p{L}0-9_\-\[\]]+\s*=\s*("|\042|\'|\047)(?:[^\\1]*?)\\1#ui', $value, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= $match;
            }
        }
        return $out;
    }

    private function srcRemovalCallback(array $match)
    {
        return $this->removalCallback($match, 'src');
    }

    private function explodedWordsCallback($matches)
    {
        return preg_replace(
                '/(?:\s+|"|\042|\'|\047|\+)*+/',
                '',
                $matches['word']
            ) . $matches['rest'];
    }

}