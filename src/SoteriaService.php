<?php

namespace devtoolboxuk\soteria;

use devtoolboxuk\soteria\handlers\Sanitise;
use devtoolboxuk\soteria\handlers\Xss;

class SoteriaService implements SoteriaInterface
{
    private static $instance = null;

    /**
     * @return Sanitise|null
     */
    public function sanitise()
    {
        if (self::$instance === null) {
            self::$instance = new Sanitise();
        }
        return self::$instance;
    }

    /**
     * @return Xss|null
     */
    public function xss()
    {
        if (self::$instance === null) {
            self::$instance = new Xss();
        }
        return self::$instance;
    }

}