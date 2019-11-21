<?php

namespace Butler\Guru\Tests;

use Bschmitt\Amqp\Consumer;
use Butler\Guru\EventRouter;
use Illuminate\Support\Facades\Log;
use Mockery as m;
use Mockery\MockInterface;
use PhpAmqpLib\Message\AMQPMessage;

class EventRouterTest extends TestCase
{
    private function makeRouter(?array $events = null): EventRouter
    {
        return new EventRouter($events ?? [
            'event.name' => [
                TestEventHandler1::class,
            ],
        ]);
    }

    private function makeMessage(string $eventName, $returnData = null): MockInterface
    {
        $message = m::mock(AMQPMessage::class);

        $message->expects()->get('routing_key')->andReturn($eventName);

        if (is_null($returnData)) {
            $message->shouldNotReceive('getBody');
        } else {
            $message->expects()->getBody()->andReturn($returnData);
        }

        return $message;
    }

    private function makeConsumer(MockInterface $message): MockInterface
    {
        $consumer = m::mock(Consumer::class);

        $consumer->expects()->acknowledge($message);

        return $consumer;
    }

    public function test_happy_path()
    {
        $message = $this->makeMessage('event.name', json_encode(['project' => 'cl1']));
        $consumer = $this->makeConsumer($message);

        $this->makeRouter()($message, $consumer);

        $this->assertDispatched(TestEventHandler1::class);
    }

    public function test_invalid_json_payload()
    {
        Log::shouldReceive('error')
            ->once()
            ->with('Couldn\'t decode json data for event.', [
                'event' => 'event.name',
                'body' => 'this is not valid json!',
                'error' => 'Syntax error',
            ]);

        $message = $this->makeMessage('event.name', 'this is not valid json!');
        $consumer = $this->makeConsumer($message);

        $this->makeRouter()($message, $consumer);

        $this->assertNotDispatched(TestEventHandler1::class);
    }

    public function test_empty_payload()
    {
        $message = $this->makeMessage('event.name', '');
        $consumer = $this->makeConsumer($message);

        $this->makeRouter()($message, $consumer);

        $this->assertDispatched(TestEventHandler1::class);
    }

    public function test_unhandled_event()
    {
        $message = $this->makeMessage('event.unknown');
        $consumer = $this->makeConsumer($message);

        $this->makeRouter()($message, $consumer);

        $this->assertNotDispatched(TestEventHandler1::class);
    }

    public function test_multiple_handlers()
    {
        $router = $this->makeRouter([
            'event.name' => [
                TestEventHandler1::class,
                TestEventHandler2::class,
            ],
        ]);

        $message = $this->makeMessage('event.name', json_encode(['project' => 'cl1']));
        $consumer = $this->makeConsumer($message);

        $router($message, $consumer);

        $this->assertDispatched(TestEventHandler1::class);
        $this->assertDispatched(TestEventHandler2::class);
    }

    public function test_only_calls_handlers_for_mapped_event()
    {
        $router = $this->makeRouter([
            'event.foo' => [
                TestEventHandler1::class,
            ],
            'event.bar' => [
                TestEventHandler2::class,
            ],
        ]);

        $message = $this->makeMessage('event.foo', json_encode(['project' => 'cl1']));
        $consumer = $this->makeConsumer($message);

        $router($message, $consumer);

        $this->assertDispatched(TestEventHandler1::class);
        $this->assertNotDispatched(TestEventHandler2::class);
    }
}
