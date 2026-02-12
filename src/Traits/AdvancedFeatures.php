<?php

declare(strict_types=1);

namespace BlitzPHP\Console\Traits;

use Throwable;

use function Ahc\Cli\t;

/**
 * Provides advanced console features and visualizations.
 *
 * @package BlitzPHP\Console\Traits
 * @mixin \BlitzPHP\Console\Command
 */
trait AdvancedFeatures
{
    use InteractsWithInput;
    use InteractsWithOutput;

    // =========================================================================
    // Time and Waiting Methods
    // =========================================================================

    /**
     * Wait for a certain number of seconds.
     *
     * @param int    $seconds  Number of seconds to wait
     * @param bool   $countdown Whether to show countdown
     * @param string $waitMsg  Wait message
     */
    public function wait(int $seconds, bool $countdown = false, string $waitMsg = 'Press any key to continue...'): void
    {
        if ($countdown) {
            $time = $seconds;

            while ($time > 0) {
                $this->writer->raw($time . '... ');
                sleep(1);
                $time--;
            }

            $this->writer->raw('' . PHP_EOL);
        } elseif ($seconds > 0) {
            sleep($seconds);
        } else {
            $this->writer->raw($waitMsg . ' ');
            $this->reader->read();
        }
    }

    /**
     * Pause execution until key press.
     *
     * @param string $message Pause message
     */
    public function pause(string $message = 'Press any key to continue...'): void
    {
        $this->wait(0, false, $message);
    }

    // =========================================================================
    // Progress and Loading Methods
    // =========================================================================

    /**
     * Execute a callback with a spinner animation.
     *
     * @param callable $callback Callback to execute
     * @param string   $message  Spinner message
     *
     * @return mixed Callback result
     */
    public function withSpinner(callable $callback, string $message = 'Processing...'): mixed
    {
        $spinner = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
        $this->write($message . ' ');

        $result = null;
        $finished = false;

        $startTime = microtime(true);
        $i = 0;

		// Execute in a separate process or with pcntl_fork if available
        if (function_exists('pcntl_fork')) {
            $pid = pcntl_fork();

            if ($pid === 0) {
				// Child process - execute callback
                try {
					$result = $callback();
					exit(0);
				} catch (Throwable) {
					exit(1);
				}
            }

			// Parent process - display spinner
            while (pcntl_waitpid($pid, $status, WNOHANG) === 0) {
                $this->write($this->cursor->left(1));
                $this->write($spinner[$i % count($spinner)]);
                usleep(100000);
                $i++;
            }

            $this->write($this->cursor->left(1))->write('✓')->eol();
        } else {
            $this->write($this->cursor->hide());
            $result = $callback();
            $this->write($this->cursor->show());
            $this->write($this->cursor->left(1))->write('✓')->eol();
        }

        return $result;
    }

    /**
     * Execute a task with a progress bar.
     *
     * @param iterable|int $items    Items to process or total count
     * @param callable     $callback Callback for each item
     */
    public function withProgressBar(iterable|int $items, callable $callback): void
    {
        $bar = $this->progress(is_iterable($items) ? count($items) : $items);

        if (is_iterable($items)) {
            foreach ($items as $key => $value) {
                $callback($value, $bar, $key);
                $bar->advance();
            }
        } else {
            $callback($bar);
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Display a live counter.
     *
     * @param callable $updater  Callback that returns current value
     * @param int      $step     Number of steps
     * @param string   $label    Counter label
     * @param int      $interval Update interval in microseconds
     */
    public function liveCounter(callable $updater, int $step = 10, string $label = 'Counter', int $interval = 1000000): void
    {
        $this->write($this->cursor->hide());

        for ($i = 0; $i < $step; $i++) {
            $value = $updater($i);
            $this->write($this->cursor->left(20));
            $this->write("{$label}: " . str_pad((string) $value, 10));
            usleep($interval);
        }

        $this->write($this->cursor->show())->eol();
    }

    // =========================================================================
    // Visualizations and Graphics
    // =========================================================================

    /**
     * Display ASCII art text.
     *
     * @param string $text Text to display as ASCII art
     * @param string $font Font style
     *
     * @return self
     */
    public function asciiArt(string $text, string $font = 'standard'): self
    {
        $fonts = [
            'standard' => [
                'A' => '  ██  ',
                'B' => '████ ',
                'C' => ' ████',
                'D' => '████ ',
                'E' => '█████',
                // ... define all characters
            ],
        ];

        if (isset($fonts[$font])) {
            $lines = [];

            foreach (str_split(strtoupper($text)) as $char) {
                if (isset($fonts[$font][$char])) {
                    $lines[] = $fonts[$font][$char];
                }
            }

            foreach ($lines as $line) {
                $this->write($line, true);
            }
        }

        return $this;
    }

    /**
     * Display a timeline of events.
     *
     * @param array<array{status?: string, description?: string}> $events Timeline events
     */
    public function timeline(array $events): self
    {
        $this->colorize('Timeline:', 'yellow');

        foreach ($events as $index => $event) {
            $status = $event['status'] ?? 'pending';
            $icon = match ($status) {
                'completed'   => '✓',
                'failed'      => '✗',
                'processing'  => '↻',
                default       => '○'
            };

            $color = match ($status) {
                'completed'   => 'green',
                'failed'      => 'red',
                'processing'  => 'yellow',
                default       => 'gray'
            };

            $this->writer->colors(sprintf(
                "  <%s>%s</end> %s",
                $color,
                $icon,
                $event['description'] ?? 'Event ' . ($index + 1)
            ))->eol();
        }

        return $this;
    }

    /**
     * Display an ASCII heatmap.
     *
     * @param array<int|float> $data   Heatmap data
     * @param array<string>    $colors Color characters
     */
    public function heatmap(array $data, array $colors = ['░', '▒', '▓', '█']): self
    {
        $max = max($data);
        $min = min($data);
        $range = $max - $min;

        foreach ($data as $value) {
            $percentage = $range > 0 ? ($value - $min) / $range : 0.5;
            $index = (int) ($percentage * (count($colors) - 1));
            $this->write($colors[$index]);
        }

        return $this->newLine();
    }

    /**
     * Display data in a grid format.
     *
     * @param array<array<mixed>> $data      Grid data
     * @param callable|null       $formatter Cell formatter callback
     */
    public function grid(array $data, ?callable $formatter = null): self
    {
        $formatter = $formatter ?? fn ($cell) => $cell;

        $colWidths = [];

        foreach ($data as $row) {
            foreach ($row as $colIndex => $cell) {
                $width = strlen((string) $formatter($cell));
                $colWidths[$colIndex] = max($colWidths[$colIndex] ?? 0, $width);
            }
        }

        foreach ($data as $row) {
            $line = '';

            foreach ($row as $colIndex => $cell) {
                $formatted = $formatter($cell);
                $line .= str_pad((string) $formatted, $colWidths[$colIndex] + 2);
            }

            $this->write($line)->eol();
        }

        return $this;
    }

    /**
     * Display a chart in ASCII format.
     *
     * @param array<string, int|float> $data   Chart data
     * @param string                   $type   Chart type ('bar' or 'pie')
     * @param int                      $height Chart height for bar charts
     */
    public function chart(array $data, string $type = 'bar', int $height = 10): self
    {
        $max = max($data);

        if ($type === 'bar') {
            foreach ($data as $label => $value) {
                $barLength = (int) (($value / $max) * $height * 2);
                $bar = str_repeat('█', $barLength);
                $this->write(sprintf("%-20s %s %d", $label, $bar, $value))->eol();
            }
        } elseif ($type === 'pie') {
            $total = array_sum($data);
            $this->colorize(t('Pie Chart'), 'yellow');

            foreach ($data as $label => $value) {
                $percentage = ($value / $total) * 100;
                $this->write(sprintf("  %s: %.1f%%", $label, $percentage))->eol();
            }
        }

        return $this;
    }

    // =========================================================================
    // Interactive Menus
    // =========================================================================

    /**
     * Display an interactive menu.
     *
     * @param string                     $title   Menu title
     * @param array<string, mixed>       $options Menu options
     * @param string|null                $default Default option
     *
     * @return mixed Selected option
     */
    public function menu(string $title, array $options, ?string $default = null): mixed
    {
        $this->colorize($title, 'yellow');

        foreach ($options as $key => $option) {
            $this->writer->colors(sprintf("  <green>%s</end> %s", $key, $option['label'] ?? $option))->eol();
        }

        $choice = $this->ask(t('Choose an option') . ' :', $default) ?? $default;

        return $options[$choice] ?? $choice;
    }

    // =========================================================================
    // Animation and Sound
    // =========================================================================

    /**
     * Display an animation.
     *
     * @param array<string> $frames     Animation frames
     * @param int           $iterations Number of iterations
     * @param int           $delay      Delay between frames in microseconds
     */
    public function animation(array $frames, int $iterations = 3, int $delay = 100000): self
    {
        $this->write($this->cursor->hide());

        for ($i = 0; $i < $iterations; $i++) {
            foreach ($frames as $frame) {
                $this->write($this->cursor->eraseLine());
                $this->write($frame);
                usleep($delay);
            }
        }

        $this->write($this->cursor->show());

        return $this;
    }

    /**
     * Play a beep sound.
     *
     * @param int $count Number of beeps
     */
    public function beep(int $count = 1): self
    {
        for ($i = 0; $i < $count; $i++) {
            $this->write("\x07");
            usleep(200000);
        }

        return $this;
    }

    // =========================================================================
    // System Integration
    // =========================================================================

    /**
     * Display a system notification.
     *
     * @param string $title   Notification title
     * @param string $message Notification message
     */
    public function notify(string $title, string $message): self
    {
        $os = PHP_OS_FAMILY;

        if ($os === 'Darwin') {
            exec("osascript -e 'display notification \"{$message}\" with title \"{$title}\"'");
        } elseif ($os === 'Linux') {
            exec("notify-send \"{$title}\" \"{$message}\"");
        } elseif ($os === 'Windows') {
            exec('powershell -Command "[System.Media.SystemSounds]::Beep.Play()"');
        }

        return $this;
    }
}
