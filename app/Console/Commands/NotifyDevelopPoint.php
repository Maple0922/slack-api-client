<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotifyDevelopPoint extends Command
{
    protected $signature = 'slack:notifyDevelopPoint';

    protected $description = '開発ポイントの進捗をSlack通知する';

    public function handle()
    {
        return Command::SUCCESS;
    }
}
