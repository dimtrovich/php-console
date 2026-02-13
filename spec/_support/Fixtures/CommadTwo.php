<?php

/**
 * This file is part of Blitz PHP - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use Dimtrovich\Console\Command;

class CommadTwo extends Command
{
    protected string $name        = 'test:command-two';
    protected string $description = 'Test command two';

    public function handle()
    {
        return 0;
    }
}
