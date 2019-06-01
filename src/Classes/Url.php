<?php

namespace devtoolboxuk\soteria\classes;

class Url
{

    private $protocol = '(?:(?:[a-z]+:)?\/\/)';
    private $auth = '(?:\\S+(?::\\S*)?@)?';
    private $ip = '^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}';
    private $tld = "(?<TLD>\.\w+?)(?:$|\/)";
    private $tldAlt = '(?:[a-z\\\\u00a1-\\\\uffff]{2,})';
    private $host = '(?:(?:[a-z\\\\u00a1-\\\\uffff0-9][-_]*)*[a-z\\\\u00a1-\\\\uffff0-9]+)';
    private $domain = '(?:\\.(?:[a-z\\\\u00a1-\\\\uffff0-9]-*)*[a-z\\\\u00a1-\\\\uffff0-9]+)*';
    private $port = '(?::\\\\d{2,5})?';
    private $path = '(?:[^\s\b\n|]*[^.,;:\?\!\@\^\$ -]*)?';

    private $standardUrlRegEx = '';
    private $stringService;

    public function __construct($options = [])
    {
        $this->stringService = new Strings();
        $this->setUrlRegex();
    }

    private function setUrlRegex()
    {
        $standardUrlRegEx = '(?:' . $this->protocol . '|www\\.)' . $this->auth . '(?:localhost|' . $this->ip . '|' . $this->host . $this->domain . '(' . $this->tld . '|' . $this->tldAlt . '))' . $this->port . $this->path;

        $this->standardUrlRegEx = '/' . $standardUrlRegEx . '/i';
    }

    function remove($str)
    {
        return preg_replace($this->standardUrlRegEx, ' ', $str);
    }
}