<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class AppStart extends Command
{
    protected $signature = 'app:start';
    protected $description = 'starting project.. ctrl+c to stop';

    public function handle(): void
    {
        $commands = [
            ['php', 'artisan', 'serve'],
            ['php', 'artisan', 'queue:work'],
        ];

        $processes = [];

        foreach ($commands as $command) {
            $process = new Process($command);
            $process->setTimeout(null);
            $process->start();

            $processes[] = $process;
        }

        while (true) {
            foreach ($processes as $index => $process) {
                if ($process->isRunning()) {
                    if ($index === 0) {
                        $this->output->write($process->getIncrementalOutput());
                        $this->output->write($process->getIncrementalErrorOutput());
                    }
                }
            }
            usleep(500000);
        }
    }
}
