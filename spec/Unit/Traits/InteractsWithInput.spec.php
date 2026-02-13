<?php

/**
 * This file is part of Dimtrovich - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Ahc\Cli\IO\Interactor;
use Dimtrovich\Console\Traits\InteractsWithInput;

use function Ahc\Cli\t;
use function Kahlan\expect;

describe('Traits / InteractsWithInput', function () {
    fileHook(
        file: ['output-io-input.test', 'output-io-writer.test'],
        beforeAll: function () {
            $this->getInputer = function ($io) {
                $inputer = new class () {
                    use InteractsWithInput;

                    protected $io;
                    protected $writer;
                    protected $reader;

                    public function setParams($io)
                    {
                        $this->io     = $io;
                        $this->reader = $io->reader();
                        $this->writer = $io->writer();

                        return $this;
                    }
                };

                return $inputer->setParams($io);
            };
        },
        beforeEach: function ($files) {
            $this->interactor = new Interactor(...$files);
            $this->writer     = $this->interactor->writer();
            $this->input      = $this->getInputer($this->interactor);
        },
    );

    describe('prompt methods', function () {
        it('prompts for input', function () {
            allow($this->interactor)->toReceive('prompt')->with('Enter name:', 'default', null, 3)->andReturn('John');

            $result = $this->input->prompt('Enter name:', 'default');

            expect($result)->toBe('John');
        });

        it('prompts for hidden input', function () {
            allow($this->interactor)->toReceive('promptHidden')->with('Password:', null, 3)->andReturn('secret');

            $result = $this->input->promptHidden('Password:');

            expect($result)->toBe('secret');
        });

        it('asks question (alias of prompt)', function () {
            allow($this->interactor)->toReceive('prompt')->with('Question?', 42, null, 3)->andReturn(42);

            $result = $this->input->ask('Question?', 42);

            expect($result)->toBe(42);
        });

        it('asks for secret (alias of promptHidden)', function () {
            allow($this->interactor)->toReceive('promptHidden')->with('Token:', null, 5)->andReturn('xyz789');

            $result = $this->input->secret('Token:', null, 5);

            expect($result)->toBe('xyz789');
        });
    });

    describe('choice methods', function () {
        beforeEach(function () {
            $this->choices = [
                'a' => 'Option A',
                'b' => 'Option B',
                'c' => 'Option C',
            ];
        });

        it('displays choices and prompts for selection', function () {
            expect($this->writer)->toReceive('question')->with('Select option:')->once();
            expect($this->writer)->toReceive('choice')->times(3);
            allow($this->interactor)->toReceive('prompt')->with('Choice')->andReturn('b');

            $result = $this->input->choice('Select option:', $this->choices, null, false);

            expect($result)->toBe('Option B');
        });

        it('returns default when choice is invalid', function () {
            allow($this->interactor)->toReceive('prompt')->andReturn('invalid');

            $result = $this->input->choice('Select:', $this->choices, 'a', false);

            expect($result)->toBe('Option A');
        });

        it('handles multiple choices', function () {
            expect($this->writer)->toReceive('question')->once();
            expect($this->writer)->toReceive('choice')->times(3);
            allow($this->interactor)->toReceive('prompt')->with('Choices (comma separated)')->andReturn('a,c');

            $result = $this->input->choices('Select multiple:', $this->choices, null, false);

            expect($result)->toBe(['Option A', 'Option C']);
        });

        it('handles case-sensitive choices', function () {
            $choices = ['A' => 'Upper A', 'a' => 'Lower a'];

            allow($this->interactor)->toReceive('prompt')->andReturn('a');

            $result = $this->input->choice('Select:', $choices, null, true);

            expect($result)->toBe('Lower a');
        });
    });

    describe('askWithCompletion', function () {
        it('accepts valid input from choices', function () {
            $choices = ['apple', 'banana', 'orange'];

            allow($this->interactor)->toReceive('prompt')->andReturn('banana');

            $result = $this->input->askWithCompletion('Fruit:', $choices, 'apple');

            expect($result)->toBe('banana');
        });

        xit('throws exception for invalid input', function () {
            $choices = ['red', 'green', 'blue'];

            allow($this->interactor)->toReceive('prompt')->andReturn('yellow');

            expect(function () {
                $this->input->askWithCompletion('Color:', ['red', 'green', 'blue']);
            })->toThrow(new InvalidArgumentException(t('Value must be one of: %s', [implode(', ', $choices)])));
        });
    });

    describe('confirm', function () {
        it('returns true for affirmative answer', function () {
            allow($this->interactor)->toReceive('confirm')->with('Continue?', 'y')->andReturn(true);

            $result = $this->input->confirm('Continue?');

            expect($result)->toBe(true);
        });

        it('returns false for negative answer', function () {
            allow($this->interactor)->toReceive('confirm')->with('Delete?', 'n')->andReturn(false);

            $result = $this->input->confirm('Delete?', 'n');

            expect($result)->toBe(false);
        });
    });
});
