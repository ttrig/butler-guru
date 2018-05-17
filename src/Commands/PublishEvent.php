<?php

namespace Butler\Guru\Commands;

use Butler\Guru\GuruEvent;
use Illuminate\Console\Command;

class PublishEvent extends Command
{
    protected $signature = 'guru:publish';

    protected $description = 'Publish a guru event';


    public function handle()
    {
        $name = $this->ask('Event name', 'servers.server.create');

        $this->line('== Context ==');

        $context = [];
        while (true) {
            $key = $this->ask("Key (empty to stop inputting context)");
            if (empty($key)) {
                break;
            }
            $value = $this->ask("Value");

            $context[$key] = $value;
        }

        event(new GuruEvent($name, $context));
    }
}
