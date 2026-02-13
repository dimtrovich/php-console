<?php

namespace Tests\Fixtures;

use Dimtrovich\Console\Command;

class CommadTwo extends Command
{
	protected string $name = 'test:command-two';
	protected string $description = 'Test command two';

	public function handle() {
		return 0;
	}
}
