<?php

namespace Butler\Guru\Drivers;

use Closure;
use Illuminate\Support\Facades\Log;

class File implements Driver
{

    public function publish($routing, $message, array $properties = [])
    {
        Log::debug("Event published: {$routing}", ['payload' => $message]);
    }

    public function consume($queue, Closure $callback, $properties = [])
    {
        throw new \BadMethodCallException("Not implemented");
    }
}
