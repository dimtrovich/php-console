<?php

declare(strict_types=1);

/**
 * This file is part of Blitz PHP - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\Console\Exceptions;

use RuntimeException;

use function Ahc\Cli\t;

/**
 * Exception thrown when a command is not found.
 */
class CommandNotFoundException extends RuntimeException
{
    /**
     * Create a new command not found exception.
     *
     * @param string $commandName Command name
     */
    public function __construct(string $commandName)
    {
        parent::__construct(t('Command %s not found', [$commandName]));
    }
}
