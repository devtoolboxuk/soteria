<?php

namespace devtoolboxuk\soteria;

use devtoolboxuk\soteria\handlers\XssClean;


class SoteriaService implements SoteriaInterface
{

    public function xss_clean($string)
    {
        return $this->xssClean($string);
    }

    private function xssClean($string)
    {
        $xss = new XssClean();
        return $xss->xss_clean($string);
    }

}