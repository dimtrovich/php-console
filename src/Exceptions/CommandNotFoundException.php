<?php

declare(strict_types=1);

namespace Dimtrovich\Console\Exceptions;

use RuntimeException;

use function Ahc\Cli\t;

/**
 * Exception thrown when a command is not found.
 *
 * @package Dimtrovich\Console\Exceptions
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
