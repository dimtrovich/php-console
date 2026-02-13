<?php

/**
 * This file is part of Dimtrovich - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use Dimtrovich\Console\Command;

class CommadOne extends Command
{
    protected string $name        = 'test:command-one';
    protected string $description = 'Test command one';

    public function handle()
    {
        return 0;
    }
}
