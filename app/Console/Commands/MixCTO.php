<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MixCTO extends Command
{
    protected $signature = 'Mix:CTO';

    protected $description = '木太久賢悟をMIXする';

    private const CHARS = [
        ":tree:",
        ":bold:",
        ":long:",
        ":smart:",
        ":understand:"
    ];

    public function handle()
    {

        collect(self::CHARS)
            ->crossJoin(self::CHARS, self::CHARS, self::CHARS, self::CHARS)
            ->map(fn ($chars) => implode("", $chars))
            // 同じ文字が3文字以上含まれているものを除外
            ->filter(fn ($chars) => collect(self::CHARS)->every(fn ($char) => substr_count($chars, $char) < 3))
            ->filter(fn ($chars, $index) => $index % 23 === 0 || $chars === ":tree::bold::long::smart::understand:")
            ->each(fn ($chars) => \Log::channel('single')->emergency($chars));
        return Command::SUCCESS;
    }
}
