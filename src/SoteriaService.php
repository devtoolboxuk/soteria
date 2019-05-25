<?php

namespace devtoolboxuk\soteria;

use devtoolboxuk\soteria\handlers\Sanitise;
use devtoolboxuk\soteria\handlers\Xss;
use devtoolboxuk\soteria\handlers\XssClean;


class SoteriaService implements SoteriaInterface
{

    public function sanitise()
    {
        return new Sanitise();
    }

    public function xss()
    {
        return new Xss();
    }



}