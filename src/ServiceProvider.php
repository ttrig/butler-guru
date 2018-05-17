<?php

namespace Butler\Guru;

use Bschmitt\Amqp\LumenServiceProvider;
use Butler\Guru\Commands\ListenForEvents;
use Butler\Guru\Commands\PublishEvent;
use Butler\Guru\Drivers\Amqp as AmqpDriver;
use Butler\Guru\Drivers\File as FileDriver;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->configure('guru');

        $driver = config('guru.driver');
        if ($driver === 'file') {
            $this->app->bind('Butler\Guru\Drivers\Driver', FileDriver::class);
            if ($this->app->runningInConsole()) {
                $this->commands([
                    PublishEvent::class,
                ]);
            }
        } elseif ($driver === 'rabbitmq') {
            $this->app->bind('Butler\Guru\Drivers\Driver', AmqpDriver::class);
            $this->app->register(LumenServiceProvider::class);
            if ($this->app->runningInConsole()) {
                $this->commands([
                    ListenForEvents::class,
                    PublishEvent::class,
                ]);
            }
        } else {
            throw new \InvalidArgumentException("Invalid guru driver: {$driver}");
        }

        $this->app->bind(EventRouter::class, function () {
            return new EventRouter(config('guru.events'));
        });
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/amqp.php', 'amqp');

        app('events')->listen(GuruEvent::class, GuruDispatcher::class);
    }
}
