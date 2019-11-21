<?php

namespace Butler\Guru;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EventHandler implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * @var string
     */
    protected $event;
    /**
     * @var array
     */
    protected $payload;

    public function __construct(string $event, array $payload)
    {
        $this->event = $event;
        $this->payload = $payload;
    }
}
