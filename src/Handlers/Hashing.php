<?php

namespace devtoolboxuk\soteria\handlers;

use devtoolboxuk\hashing\HashingService;

class Hashing
{

    private $hashingService;

    function __construct()
    {
        $this->hashingService = new HashingService();
    }


    function __call($type, $arguments)
    {
        return $this->build($type, $arguments);
    }

    /**
     * @param $type
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function build($type, $arguments)
    {

        try {
            if (isset($arguments[0])) {
                return $this->hashingService->$type($arguments[0]);
            } else {
                return $this->hashingService->$type();
            }

        } catch (\Exception $e) {
            throw new \Exception(sprintf('"%s" is not a valid hashing function', $type));
        }
    }

}