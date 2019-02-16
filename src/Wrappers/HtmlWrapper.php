<?php

namespace soteria\secure\Wrappers;

use soteria\secure\Wrappers\Resources\HtmlArray;
use soteria\secure\Wrappers\Resources\HtmlAttributesArray;

class HtmlWrapper extends Wrapper
{

    private $name;

    function getWrapperName()
    {
        $this->name = str_replace(__NAMESPACE__ . "\\", "", __CLASS__);
        return $this->name;
    }

    public function process()
    {
        $this->replaceHtml();
        $this->removeEmptyAttributes();
    }

    private function replaceHtml()
    {
        $value = $this->getValue();
        $htmlArray = new HtmlArray();
        $tags = implode('|', $htmlArray->getData());
        $value = (string)preg_replace_callback(
            '#<(?<start>/*\s*)(?<content>' . $tags . ')(?<end>[^><]*)(?<rest>[><]*)#i',
            [
                $this,
                'sanitizeHtml',
            ],
            $value
        );
        $this->setValue($value);
    }

    private function removeEmptyAttributes()
    {
        $value = $this->getValue();

        $HTMLattributesArray = new HtmlAttributesArray();

        foreach ($HTMLattributesArray->getData() as $string) {

            $value = str_replace(
                $string . '=""',
                $this->getReplacementValue(),
                $value
            );
        }

        $this->setValue($value);
    }

    private function sanitizeHtml(array $matches)
    {
        return '&lt;' . $matches['start'] . $matches['content'] . $matches['end']
            . str_replace(
                [
                    '>',
                    '<',
                ],
                [
                    '&gt;',
                    '&lt;',
                ],
                $matches['rest']
            );
    }
}