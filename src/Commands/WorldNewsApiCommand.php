<?php

namespace Amsaid\WorldNewsApi\Commands;

use Illuminate\Console\Command;

class WorldNewsApiCommand extends Command
{
    public $signature = 'news';

    public $description = 'World news api';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
