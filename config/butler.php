<?php

return [

    'guru' => [

        'driver' => env('BUTLER_GURU_DRIVER', 'file'),

        'events' => [
            'an.example.event' => [
                EventHandler::class,
            ],
        ],

    ],

];
