<?php

namespace Tests\Fixtures;

use BlitzPHP\Console\Command;

class CommadOne extends Command
{
	protected string $name = 'test:command-one';
	protected string $description = 'Test command one';

	public function handle() {
		return 0;
	}
}
