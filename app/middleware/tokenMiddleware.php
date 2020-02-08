<?php

use Luna\Core\Middleware;
class token extends Middleware
{
    public function __invoke()
    {
        return true;
    }
}
