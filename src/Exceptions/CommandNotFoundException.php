<?php

namespace BlitzPHP\Console\Exceptions;

use RuntimeException;

class CommandNotFoundException extends RuntimeException
{
	public function __construct(string $commandName)
	{
		parent::__construct("La commande '{$commandName}' est introuvable.");
	}
}
