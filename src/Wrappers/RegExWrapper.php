<?php

namespace soteria\secure\Wrappers;

class RegExWrapper extends Wrapper
{

    private $_never_allowed_regex = [
        // default javascript
        'javascript\s*:',
        // default javascript
        '(\(?document\)?|\(?window\)?(\.document)?)\.(location|on\w*)',
        // Java: jar-protocol is an XSS hazard
        'jar\s*:',
        // Mac (will not run the script, but open it in AppleScript Editor)
        'applescript\s*:',
        // IE: https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet#VBscript_in_an_image
        'vbscript\s*:',
        // IE, surprise!
        'wscript\s*:',
        // IE
        'jscript\s*:',
        // IE: https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet#VBscript_in_an_image
        'vbs\s*:',
        // https://html5sec.org/#behavior
        'behavior\s:',
        // ?
        'Redirect\s+30\d',
        // data-attribute + base64
        "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?",
        // remove Netscape 4 JS entities
        '&\s*\{[^}]*(\}\s*;?|$)',
        // old IE, old Netscape
        'expression\s*(\(|&\#40;)',
        // old Netscape
        'mocha\s*:',
        // old Netscape
        'livescript\s*:',
        // default view source
        'view-source\s*:',
    ];


    public function process()
    {
        $value = $this->getValue();
        foreach ($this->_never_allowed_regex as $regEx) {
            $value = preg_replace('#' . $regEx . '#is', $this->getReplacementValue(), $value);
        }
        $this->setValue($value);

    }

}