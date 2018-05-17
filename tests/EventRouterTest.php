<?php

namespace Butler\Guru\Tests;

use Bschmitt\Amqp\Consumer;
use Butler\Guru\EventRouter;
use Illuminate\Support\Facades\Log;
use Mockery as m;
use PhpAmqpLib\Message\AMQPMessage;

class EventRouterTest extends TestCase
{

    public function test_basic_happypath()
    {
        $router = new EventRouter([
            'ip.ip.added' => [
                TestEventHandler1::class,
            ],
        ]);

        $message = m::mock(AMQPMessage::class);
        $consumer = m::mock(Consumer::class);

        $consumer->shouldReceive('acknowledge');
        $message->shouldReceive('get')->once()->with('routing_key')->andReturn('ip.ip.added');
        $message->shouldReceive('getBody')->once()->andReturn(json_encode(['project' => 'cl1']));

        $this->expectsJobs(TestEventHandler1::class);

        $router($message, $consumer);
    }

    public function test_invalid_json_payload()
    {
        $router = new EventRouter([
            'ip.ip.added' => [
                TestEventHandler1::class,
            ],
        ]);

        $message = m::mock(AMQPMessage::class);
        $consumer = m::mock(Consumer::class);

        Log::shouldReceive('error')
            ->once()
            ->with(
                "Couldn't decode json data for event.",
                ['event' => 'ip.ip.added', 'body' => 'this is not valid json!', 'error' => 'Syntax error']
            );

        $consumer->shouldReceive('acknowledge');
        $message->shouldReceive('get')->once()->with('routing_key')->andReturn('ip.ip.added');
        $message->shouldReceive('getBody')->once()->andReturn("this is not valid json!");

        $router($message, $consumer);
    }

    public function test_empty_payload()
    {
        $router = new EventRouter([
            'ip.ip.added' => [
                TestEventHandler1::class,
            ],
        ]);

        $message = m::mock(AMQPMessage::class);
        $consumer = m::mock(Consumer::class);

        $consumer->shouldReceive('acknowledge');
        $message->shouldReceive('get')->once()->with('routing_key')->andReturn('ip.ip.added');
        $message->shouldReceive('getBody')->once()->andReturn("");

        $this->expectsJobs(TestEventHandler1::class);

        $router($message, $consumer);
    }

    public function test_unhandled_event()
    {
        $router = new EventRouter([
            'ip.ip.added' => [
                TestEventHandler1::class,
            ],
        ]);

        $message = m::mock(AMQPMessage::class);
        $consumer = m::mock(Consumer::class);

        $consumer->shouldReceive('acknowledge');
        $message->shouldReceive('get')->once()->with('routing_key')->andReturn('server.server.created');
        $message->shouldNotReceive('getBody');

        $router($message, $consumer);
    }

    public function test_multiple_handlers()
    {
        $router = new EventRouter([
            'ip.ip.added' => [
                TestEventHandler1::class,
                TestEventHandler2::class,
            ],
        ]);

        $message = m::mock(AMQPMessage::class);
        $consumer = m::mock(Consumer::class);

        $consumer->shouldReceive('acknowledge');
        $message->shouldReceive('get')->once()->with('routing_key')->andReturn('ip.ip.added');
        $message->shouldReceive('getBody')->once()->andReturn(json_encode(['project' => 'cl1']));

        $this->expectsJobs([
            TestEventHandler1::class,
            TestEventHandler2::class,
        ]);

        $router($message, $consumer);
    }

    public function test_only_calls_handlers_for_mapped_event()
    {
        $router = new EventRouter([
            'ip.ip.added' => [
                TestEventHandler1::class,
            ],
            'ip.ip.removed' => [
                TestEventHandler2::class,
            ],
        ]);

        $message = m::mock(AMQPMessage::class);
        $consumer = m::mock(Consumer::class);

        $consumer->shouldReceive('acknowledge');
        $message->shouldReceive('get')->once()->with('routing_key')->andReturn('ip.ip.added');
        $message->shouldReceive('getBody')->once()->andReturn(json_encode(['project' => 'cl1']));

        $this->expectsJobs([
            TestEventHandler1::class,
        ]);

        $router($message, $consumer);
    }
}
