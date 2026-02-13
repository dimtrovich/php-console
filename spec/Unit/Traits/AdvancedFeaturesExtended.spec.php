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

describe('Traits / AdvancedFeatures - Extended', function () {
    fileHook(
        file: ['output-advanced-extended-input.test', 'output-advanced-extended-writer.test'],
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

    describe('heatmap', function () {
        it('displays heatmap with default colors', function () {
            $data = [10, 20, 5, 30, 15];

            expect($this->writer)->toReceive('write')->times(5);
            expect($this->writer)->toReceive('eol')->once();

            $result = $this->advanced->heatmap($data);

            expect($result)->toBe($this->advanced);
        });

        it('displays heatmap with custom colors', function () {
            $data   = [1, 2, 3, 4, 5];
            $colors = ['▁', '▂', '▃', '▄', '▅', '▆', '▇', '█'];

            expect($this->writer)->toReceive('write')->with(Arg::toMatch(fn ($actual) => in_array($actual, $colors, true)))->times(5);

            $this->advanced->heatmap($data, $colors);
        });

        it('handles uniform data', function () {
            $data = [5, 5, 5, 5];

            expect($this->writer)->toReceive('write')->times(4);

            $this->advanced->heatmap($data);
        });

        it('handles empty data', function () {
            $data = [];

            expect($this->writer)->not->toReceive('write');
            expect($this->writer)->not->toReceive('eol');

            $this->advanced->heatmap($data);
        });
    });

    describe('grid with formatter', function () {
        it('handles empty grid', function () {
            expect($this->writer)->not->toReceive('write');

            $this->advanced->grid([]);
        });

        it('handles formatter returning non-string', function () {
            $data      = [[1, 2], [3, 4]];
            $formatter = fn ($cell) => $cell * 2;

            expect($this->writer)->toReceive('write')->with(Arg::toContain('2'))->once();

            $this->advanced->grid($data, $formatter);
        });

        it('calculates column widths correctly', function () {
            $data = [
                ['Short', 'Very long text'],
                ['Tiny', 'Small'],
            ];

            expect($this->writer)->toReceive('write')->with(Arg::toContain('Short  '))->once();
            expect($this->writer)->toReceive('write')->with(Arg::toContain('Tiny   '))->once();

            $this->advanced->grid($data);
        });
    });

    describe('menu with complex options', function () {
        it('handles menu with array options', function () {
            $options = [
                '1' => ['label' => 'Option 1', 'value' => 100],
                '2' => ['label' => 'Option 2', 'value' => 200],
            ];

            allow($this->interactor)->toReceive('prompt')->andReturn('2');

            $result = $this->advanced->menu('Menu', $options);

            expect($result)->toBe(['label' => 'Option 2', 'value' => 200]);
        });

        it('handles menu with simple string options', function () {
            $options = [
                'yes' => 'Yes',
                'no'  => 'No',
            ];

            allow($this->interactor)->toReceive('prompt')->andReturn('no');

            $result = $this->advanced->menu('Confirm?', $options);

            expect($result)->toBe('No');
        });

        it('returns choice key when option not found', function () {
            $options = ['a' => 'Apple'];

            allow($this->interactor)->toReceive('prompt')->andReturn('b');

            $result = $this->advanced->menu('Fruit', $options);

            expect($result)->toBe('b');
        });

        it('uses default when no input', function () {
            $options = ['1' => 'One', '2' => 'Two'];

            allow($this->interactor)->toReceive('prompt')->andReturn(null);

            $result = $this->advanced->menu('Choose', $options, '2');

            expect($result)->toBe('Two');
        });
    });

    describe('chart edge cases', function () {
        it('handles empty data for chart', function () {
            expect($this->writer)->not->toReceive('write');

            $this->advanced->chart([], 'bar');
        });

        it('handles zero values in bar chart', function () {
            $data = ['A' => 0, 'B' => 10, 'C' => 0];

            expect($this->writer)->toReceive('write')->times(3);

            $this->advanced->chart($data, 'bar');
        });

        it('handles single value in pie chart', function () {
            $data = ['A' => 100];

            expect($this->writer)->toReceive('write')->with(Arg::toContain('100.0%'))->once();

            $this->advanced->chart($data, 'pie');
        });
    });

    describe('animation edge cases', function () {
        it('handles empty frames', function () {
            expect($this->writer)->toReceive('write')->times(2); // hide et show

            $this->advanced->animation([]);
        });

        it('handles single frame', function () {
            $frames = ['*'];

            expect($this->writer)->toReceive('write')->times(4); // hide, frame, eraseLine, show

            $this->advanced->animation($frames, 1);
        });

        it('handles zero iterations', function () {
            $frames = ['◐', '◓'];

            expect($this->writer)->toReceive('write')->times(2); // hide et show seulement

            $this->advanced->animation($frames, 0);
        });
    });

    describe('notification edge cases', function () {
        it('handles different OS families', function () {
            // On ne peut pas tester l'exécution réelle, mais on peut vérifier qu'il n'y a pas d'erreur

            $oses = ['Darwin', 'Linux', 'Windows', 'Unknown'];

            foreach ($oses as $os) {
                allow('PHP_OS_FAMILY')->toBe($os);

                $result = $this->advanced->notify('Title', 'Message');

                expect($result)->toBe($this->advanced);
            }
        });
    });
});
