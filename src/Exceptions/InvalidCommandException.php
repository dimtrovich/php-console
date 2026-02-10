<?php

namespace BlitzPHP\Console\Exceptions;

use RuntimeException;

class InvalidCommandException extends RuntimeException
{
	public function __construct(string $commandName)
	{
		parent::__construct("La commande '{$commandName}' n'est pas valide.");
	}
}
