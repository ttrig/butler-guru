<?php

namespace Butler\Guru;

class GuruEvent
{
    public $event;
    public $payload = [];

    /**
     * Create a new event instance.
     *
     * @param string $event
     * @param array $payload
     */
    public function __construct(string $event, array $payload)
    {
        $this->event = $event;
        $this->payload = $payload;
    }
}
