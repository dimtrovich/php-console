<?php

declare(strict_types=1);

namespace Dimtrovich\Console\Exceptions;

use RuntimeException;

use function Ahc\Cli\t;

/**
 * Exception thrown when a command is invalid.
 *
 * @package Dimtrovich\Console\Exceptions
 */
class InvalidCommandException extends RuntimeException
{
    /**
     * Create a new invalid command exception.
     *
     * @param string $commandName Command name
     */
    public function __construct(string $commandName)
    {
		parent::__construct(t('Command "%s" is invalid', [$commandName]));
    }
}
