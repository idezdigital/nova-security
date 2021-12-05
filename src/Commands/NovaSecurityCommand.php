<?php

namespace Idez\NovaSecurity\Commands;

use Illuminate\Console\Command;

class NovaSecurityCommand extends Command
{
    public $signature = 'nova-security';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
