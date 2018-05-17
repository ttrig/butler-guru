<?php

namespace Butler\Guru;

use Bschmitt\Amqp\Consumer;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class EventRouter
{
    /**
     * @var array
     */
    private $events;

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function __invoke(AMQPMessage $message, Consumer $consumer)
    {
        $event = $message->get('routing_key');

        $handlers = $this->events[$event] ?? null;

        if (empty($handlers)) {
            $consumer->acknowledge($message);
            return;
        }

        $body = $message->getBody();
        $payload = [];

        if ($body) {
            $payload = json_decode($body, true);

            if ($payload === null) {
                Log::error("Couldn't decode json data for event.", [
                    'event' => $event,
                    'body' => $body,
                    'error' => json_last_error_msg(),
                ]);
                $consumer->acknowledge($message);
                return;
            }
        }

        foreach ($handlers as $handler) {
            dispatch(new $handler($event, $payload));
        }

        $consumer->acknowledge($message);
    }
}
