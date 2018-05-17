<?php

namespace Butler\Guru\Drivers;

use Closure;

class Amqp implements Driver
{

    public function publish($routing, $message, array $properties = [])
    {
        \Amqp::publish($routing, $message, $properties);
    }

    public function consume($queue, Closure $callback, $properties = [])
    {
        \Amqp::consume($queue, $callback, $properties);
    }
}
