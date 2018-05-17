<?php

namespace Butler\Guru;

use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;
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
        $driver = config('guru.driver', 'file');
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
        $this->setupConfig($this->app);
        app('events')->listen(GuruEvent::class, GuruDispatcher::class);
    }

    private function setupConfig($app)
    {
        $guruSource = realpath(__DIR__ . '/../config/guru.php');
        $amqpSource = realpath(__DIR__ . '/../config/amqp.php');

        if ($app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$guruSource => config_path('guru.php')]);
            $this->publishes([$amqpSource => config_path('amqp.php')]);
        } elseif ($app instanceof LumenApplication) {
            $app->configure('guru');
            $this->mergeConfigFrom($amqpSource, 'amqp');
        }
    }
}
