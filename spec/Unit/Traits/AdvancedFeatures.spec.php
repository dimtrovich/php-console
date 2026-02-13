<?php

/**
 * This file is part of Blitz PHP - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Ahc\Cli\IO\Interactor;
use Dimtrovich\Console\Components\ProgressBar;
use Dimtrovich\Console\Overrides\Cursor;
use Dimtrovich\Console\Traits\AdvancedFeatures;
use Kahlan\Arg;

use function Kahlan\expect;

describe('Traits / AdvancedFeatures', function () {
    fileHook(
        file: ['output-advanced-input.test', 'output-advanced-writer.test'],
        beforeAll: function () {
            $this->getAdvanced = function ($io) {
                $advanced = new class () {
                    use AdvancedFeatures;

                    protected $io;
                    protected $writer;
                    protected $reader;
                    protected $terminal;
                    public $cursor;

                    public function setParams($io)
                    {
                        $this->io       = $io;
                        $this->reader   = $io->reader();
                        $this->writer   = $io->writer();
                        $this->terminal = $this->writer->terminal();
                        $this->cursor   = new Cursor();

                        return $this;
                    }

                    public function progress($total = null)
                    {
                        return new ProgressBar($total, $this->writer);
                    }
                };

                return $advanced->setParams($io);
            };
        },
        beforeEach: function ($files) {
            $this->interactor = new Interactor(...$files);
            $this->writer     = $this->interactor->writer();
            $this->reader     = $this->interactor->reader();
            $this->advanced   = $this->getAdvanced($this->interactor);
            $this->cursor     = $this->advanced->cursor;
        },
    );

    describe('wait methods', function () {
        it('waits for seconds without countdown', function () {
            $start = microtime(true);
            $this->advanced->wait(1);
            $elapsed = microtime(true) - $start;

            expect($elapsed >= 0.9)->toBe(true);
        });

        it('shows countdown when enabled', function () {
            expect($this->writer)->toReceive('raw')->times(4); // 3... 2... 1... + PHP_EOL

            $this->advanced->wait(3, true);
        });

        it('pauses for key press', function () {
            expect($this->writer)->toReceive('raw')->with('Press any key to continue... ')->once();
            expect($this->reader)->toReceive('read')->once();

            $this->advanced->pause();
        });
    });

    describe('spinner', function () {
        it('executes callback with spinner', function () {
            $callback = fn () => 'result';

            expect($this->writer)->toReceive('write');

            $result = $this->advanced->withSpinner($callback, 'Processing');

            expect($result)->toBe('result');
        });

        it('handles callback without pcntl_fork', function () {
            // Simuler pcntl_fork indisponible
            skipIf(! function_exists('pcntl_fork'));

            expect($this->cursor)->toReceive('hide')->once();
            expect($this->cursor)->toReceive('show')->once();

            $callback = fn () => 'no-fork';

            $result = $this->advanced->withSpinner($callback);

            expect($result)->toBe('no-fork');
        });
    });

    describe('progress bar with callback', function () {
        it('executes with progress bar for array', function () {
            $items     = [1, 2, 3, 4, 5];
            $processed = 0;

            $this->advanced->withProgressBar($items, function ($item, $bar, $key) use (&$processed) {
                $processed++;
                expect($bar)->toBeAnInstanceOf(ProgressBar::class);
            });

            expect($processed)->toBe(5);
        });

        it('executes with progress bar for count', function () {
            $called = false;

            $this->advanced->withProgressBar(10, function ($bar) use (&$called) {
                $called = true;
                expect($bar)->toBeAnInstanceOf(ProgressBar::class);
            });

            expect($called)->toBe(true);
        });
    });

    describe('live counter', function () {
        it('displays live counter', function () {
            expect($this->cursor)->toReceive('hide')->once();
            expect($this->cursor)->toReceive('show')->once();
            expect($this->writer)->toReceive('write');

            $updater = fn ($i) => $i * 10;

            $this->advanced->liveCounter($updater, 5, 'Count', 1000);
        });
    });

    describe('timeline', function () {
        it('displays timeline of events', function () {
            $events = [
                ['status' => 'completed', 'description' => 'Task 1'],
                ['status'      => 'processing', 'description' => 'Task 2'],
                ['status'      => 'failed', 'description' => 'Task 3'],
                ['description' => 'Task 4'], // default status
            ];

            expect($this->writer)->toReceive('colors')->times(5); // 4 + PHP_EOL

            $result = $this->advanced->timeline($events);

            expect($result)->toBe($this->advanced);
        });
    });

    describe('chart', function () {
        it('displays bar chart', function () {
            $data = ['A' => 10, 'B' => 20, 'C' => 5];

            expect($this->writer)->toReceive('write')->times(3);

            $result = $this->advanced->chart($data, 'bar', 5);

            expect($result)->toBe($this->advanced);
        });

        it('displays pie chart', function () {
            $data = ['Linux' => 50, 'Windows' => 30, 'Mac' => 20];

            expect($this->writer)->toReceive('colors')->with(Arg::toContain('Pie Chart'))->once();
            expect($this->writer)->toReceive('write')->times(4); // 3 + PHP_EOL

            $this->advanced->chart($data, 'pie');
        });
    });

    describe('grid', function () {
        it('displays data in grid format', function () {
            $data = [
                ['Name', 'Age', 'City'],
                ['John', 30, 'New York'],
                ['Jane', 25, 'London'],
            ];

            expect($this->writer)->toReceive('write')->times(3);

            $result = $this->advanced->grid($data);

            expect($result)->toBe($this->advanced);
        });

        it('displays grid with custom formatter', function () {
            $data      = [[1, 2], [3, 4]];
            $formatter = fn ($cell) => "[{$cell}]";

            expect($this->writer)->toReceive('write')->with(Arg::toContain('[1]'))->once();

            $this->advanced->grid($data, $formatter);
        });
    });

    describe('menu', function () {
        it('displays interactive menu', function () {
            $options = [
                '1' => ['label' => 'Create'],
                '2' => ['label' => 'Update'],
                '3' => 'Delete', // simple string
            ];

            expect($this->writer)->toReceive('colors')->times(4); // 3 + PHP_EOL
            allow($this->interactor)->toReceive('prompt')->with('Choose an option :', null, null, 3)->andReturn('2');

            $result = $this->advanced->menu('Actions', $options);

            expect($result)->toBe(['label' => 'Update']);
        });

        it('returns default option when no choice', function () {
            $options = ['yes' => 'Yes', 'no' => 'No'];

            allow($this->interactor)->toReceive('prompt')->andReturn(null);

            $result = $this->advanced->menu('Confirm?', $options, 'yes');

            expect($result)->toBe('Yes');
        });
    });

    describe('animation', function () {
        it('displays animation frames', function () {
            $frames = ['◐', '◓', '◑', '◒'];

            expect($this->cursor)->toReceive('hide')->once();
            expect($this->cursor)->toReceive('show')->once();
            expect($this->writer)->toReceive('write')->times(26); // ((4 frames * 3 iterations) * 2) + hide cursor + show cursor

            $result = $this->advanced->animation($frames, 3, 1000);

            expect($result)->toBe($this->advanced);
        });
    });

    describe('beep', function () {
        it('plays beep sound', function () {
            expect($this->writer)->toReceive('write')->with("\x07")->times(3);

            $result = $this->advanced->beep(3);

            expect($result)->toBe($this->advanced);
        });
    });

    describe('notify', function () {
        it('sends system notification', function () {
            // On ne peut pas tester l'exécution réelle, mais on peut vérifier qu'il n'y a pas d'erreur
            $result = $this->advanced->notify('Title', 'Message');

            expect($result)->toBe($this->advanced);
        });
    });
});
