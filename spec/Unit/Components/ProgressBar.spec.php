<?php

use Ahc\Cli\Output\Writer;
use BlitzPHP\Console\Components\ProgressBar;
use Kahlan\Arg;

use function Kahlan\expect;

describe('ProgressBar', function () {

    beforeAll(function() {
		$this->outputFile = __DIR__ . '/../../output-progressbar.test';

		if (! is_dir($dirname = pathinfo($this->outputFile, PATHINFO_DIRNAME))) {
			mkdir($dirname);
		}
		file_put_contents($this->outputFile, '', LOCK_EX);
	});

    beforeEach(function () {
        $this->writer = new Writer($this->outputFile);
		$this->progress = new ProgressBar(100, $this->writer);
    });

	afterEach(function() {
		file_put_contents($this->outputFile, '', LOCK_EX);
	});

	afterAll(function() {
		if (file_exists($this->outputFile)) {
			unlink($this->outputFile);
		}
	});

    describe('constructor', function () {

        it('initializes with total steps', function () {
            $progress = new ProgressBar(50, $this->writer);

            expect($progress)->toBeAnInstanceOf(ProgressBar::class);
        });

        it('records start time', function () {
            $reflection = new ReflectionClass($this->progress);
            $startTime = $reflection->getProperty('startTime');
            $startTime->setAccessible(true);

            expect($startTime->getValue($this->progress))->toBeGreaterThan(0);
        });
    });

    describe('advancement', function () {

        it('advances progress', function () {
            expect($this->writer)->toReceive('write')->times(2);

            $this->progress->advance();
        });

        it('advances with custom step', function () {
            expect($this->writer)->toReceive('write')->times(2);

            $this->progress->advance(5);
        });

        it('advances with message', function () {
            expect($this->writer)->toReceive('write')->with(Arg::toContain('Processing'))->once();

            $this->progress->advanceWithMessage(1, 'Processing item 1');

            $reflection = new ReflectionClass($this->progress);
            $messages = $reflection->getProperty('messages');
            $messages->setAccessible(true);

            expect($messages->getValue($this->progress))->toContain('Processing item 1');
        });
    });

    describe('statistics', function () {

        it('shows statistics', function () {
            $this->progress->advance(25);

            expect($this->writer)->toReceive('colors')->with(Arg::toContain('Statistics'))->once();
            expect($this->writer)->toReceive('colors')->with(Arg::toContain('items/s'))->once();

            $this->progress->showStats();
        });

        it('shows messages in statistics', function () {
            $this->progress->advanceWithMessage(1, 'Item 1 processed');
            $this->progress->advanceWithMessage(1, 'Item 2 processed');

            expect($this->writer)->toReceive('write')->with(Arg::toContain('Item 1 processed'))->once();
            expect($this->writer)->toReceive('write')->with(Arg::toContain('Item 2 processed'))->once();

            $this->progress->showStats();
        });

        it('calculates speed correctly', function () {
            $this->progress->advance(50);

            // Mock microtime pour un test déterministe
            $reflection = new ReflectionClass($this->progress);
            $startTime = $reflection->getProperty('startTime');
            $startTime->setAccessible(true);
            $startTime->setValue($this->progress, microtime(true) - 5); // 5 secondes écoulées

            expect($this->writer)->toReceive('colors')->with(Arg::toContain('10.00 items/s'))->once();

            $this->progress->showStats();
        });
    });

    describe('display', function () {

        it('displays progress bar with percentage', function () {
            $this->progress->advance(50);

            expect($this->writer)->toReceive('write')->with(Arg::toContain('[', ']', '50%'))->once();

            $this->progress->display();
        });

        xit('displays progress bar with custom width', function () {
            $this->progress->advance(30);

            expect($this->writer)->toReceive('write')->with(Arg::toMatch('/█{15}░{35}/'))->once();

            $this->progress->display();
        });

        it('does not display when total is not set', function () {
            $progress = new ProgressBar(null, $this->writer);

            expect($this->writer)->not->toReceive('write')->with(Arg::toContain('[', ']'));

            $progress->display();
        });
    });
});
