<?php

namespace devtoolboxuk\soteria;

use devtoolboxuk\soteria\handlers\Sanitise;
use devtoolboxuk\soteria\handlers\Xss;

class SoteriaService implements SoteriaInterface
{
    private static $instance = null;

    /**
     * @param bool $force
     * @return Sanitise|null
     */
    public function sanitise($force = false)
    {
        if (self::$instance === null || $force) {
            self::$instance = new Sanitise();
        }
        return self::$instance;
    }

    /**
     * @param bool $force
     * @return Xss|null
     */
    public function xss($force = false)
    {
        if (self::$instance === null || $force) {
            self::$instance = new Xss();
        }
        return self::$instance;
    }

}