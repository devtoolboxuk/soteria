<?php

namespace devtoolboxuk\soteria;

interface SoteriaInterface
{
    public function sanitise($force);

    public function xss($force);
}