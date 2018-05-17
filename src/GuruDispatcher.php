<?php

namespace Butler\Guru;

use Butler\Guru\Drivers\Driver as GuruDriver;

class GuruDispatcher
{
    /**
     * @var GuruDriver
     */
    private $driver;

    public function __construct(GuruDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Handle the event.
     *
     * @param GuruEvent $event
     * @return void
     */
    public function handle(GuruEvent $event)
    {
        $this->driver->publish($event->event, json_encode($event->payload));
    }
}
