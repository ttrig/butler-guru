<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Define which configuration should be used
    |--------------------------------------------------------------------------
    */

    'use' => 'production',

    /*
    |--------------------------------------------------------------------------
    | AMQP properties separated by key
    |--------------------------------------------------------------------------
    */

    'properties' => [

        'production' => [
            'host' => env('GURU_HOST'),
            'port' => env('GURU_PORT'),
            'username' => env('GURU_USERNAME'),
            'password' => env('GURU_PASSWORD'),
            'vhost' => env('GURU_VHOST'),
            'connect_options' => [
                'heartbeat' => 10,
                'read_write_timeout' => 30
            ],
            'ssl_options' => [],

            'exchange' => env('GURU_EXCHANGE'),
            'exchange_type' => 'topic',
            'exchange_passive' => false,
            'exchange_durable' => true,
            'exchange_auto_delete' => false,
            'exchange_internal' => false,
            'exchange_nowait' => false,
            'exchange_properties' => [],

            'queue_force_declare' => true,
            'queue_passive' => false,
            'queue_durable' => true,
            'queue_exclusive' => false,
            'queue_auto_delete' => false,
            'queue_nowait' => false,
            'queue_properties' => ['x-ha-policy' => ['S', 'all']],

            'consumer_tag' => '',
            'consumer_no_local' => false,
            'consumer_no_ack' => false,
            'consumer_exclusive' => false,
            'consumer_nowait' => false,
            'timeout' => 0,
            'persistent' => true,
            'queue' => env('GURU_QUEUE'),
            'routing' => env('GURU_ROUTING'),
        ],
    ],
];
