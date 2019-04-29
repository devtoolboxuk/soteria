<?php

namespace devtoolboxuk\soteria\Wrappers;


use devtoolboxuk\soteria\Wrappers\Resources\AttributesArray;

class AttributeWrapper extends Wrapper
{

    private $name;

    function getWrapperName()
    {
        $this->name = str_replace(__NAMESPACE__ . "\\", "", __CLASS__);
        return $this->name;
    }


    public function process()
    {

        $value = $this->getValue();

        $attributesArray = new AttributesArray();

        if (in_array('style', $attributesArray->getData(), true)) {
            do {
                $count = $temp_count = 0;
                $value = preg_replace(
                    '/(<[^>]+)(?<!\w)(style\s*=\s*"(?:[^"]*?)"|style\s*=\s*\'(?:[^\']*?)\')/i',
                    '$1' . $this->getReplacementValue(),
                    $value,
                    -1,
                    $temp_count
                );
                $count += $temp_count;
            } while ($count);
        }

        $attributes_string = implode('|', $attributesArray->getData());
        do {
            $count = $temp_count = 0;

            // find occurrences of illegal attribute strings with and without quotes (042 ["] and 047 ['] are octal quotes)
            $value = (string)preg_replace(
                '/(.*)((?:<[^>]+)(?<!\w))(?:' . $attributes_string . ')(?:\s*=\s*)(?:(?:\'|\047)(?:.*?)(?:\'|\047)|(?:"|\042)(?:.*?)(?:"|\042))(.*)/is',
                '$1$2' . $this->getReplacementValue() . '$3$4',
                $value,
                -1,
                $temp_count
            );
            $count += $temp_count;

            $value = (string)preg_replace(
                '/(.*)(<[^>]+)(?<!\w)(?:' . $attributes_string . ')\s*=\s*(?:[^\s>]*)(.*)/is',
                '$1$2' . $this->getReplacementValue() . '$3',
                $value,
                -1,
                $temp_count
            );
            $count += $temp_count;
        } while ($count);

        $this->setValue($value);
    }
}