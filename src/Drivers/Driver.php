<?php

namespace Butler\Guru\Drivers;

use Closure;

interface Driver
{
    public function publish($routing, $message, array $properties = []);

    public function consume($queue, Closure $callback, $properties = []);
}
