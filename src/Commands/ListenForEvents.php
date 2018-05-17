<?php

namespace Butler\Guru\Commands;

use Bschmitt\Amqp\Consumer;
use Butler\Guru\Drivers\Driver as GuruDriver;
use Butler\Guru\EventRouter;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

class ListenForEvents extends Command
{
    protected $signature = 'guru:listen';

    protected $description = 'Listen for Guru events and dispatch handler jobs';

    /**
     * @param EventRouter $router
     * @param GuruDriver $driver
     */
    public function handle(EventRouter $router, GuruDriver $driver)
    {
        $driver->consume(
            config('amqp.properties.production.queue'),
            function (AMQPMessage $message, Consumer $consumer) use ($router) {
                $router($message, $consumer);
            }
        );
    }
}
