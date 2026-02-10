<?php

declare(strict_types=1);

namespace BlitzPHP\Console\Traits;

use Ahc\Cli\Helper\Terminal;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use BlitzPHP\Console\Overrides\Cursor;
use BlitzPHP\Console\Overrides\ProgressBar;

/**
 * Provides interaction with console output.
 *
 * @property Color    $color
 * @property Cursor   $cursor
 * @property Terminal $terminal
 * @property Writer   $writer
 *
 * @package BlitzPHP\Console\Traits
 */
trait InteractsWithOutput
{
    /**
     * Write a message with optional color.
     *
     * @param string      $message    Message to write
     * @param string|null $color      Color name
     * @param int         $verbosity  Verbosity level
     *
     * @return self
     */
    public function line(string $message, ?string $color = null, int $verbosity = 1): self
    {
        if ($color !== null) {
            return $this->colorize($message, $color);
        }

        return $this->write($message)->eol();
    }

    /**
     * Write a question message.
     *
     * @param string $message Question message
     *
     * @return self
     */
    public function question(string $message): self
    {
        $this->writer->question($message);

        return $this;
    }

    /**
     * Write a comment message.
     *
     * @param string $text Comment text
     * @param bool   $eol  Whether to add end of line
     *
     * @return self
     */
    public function comment(string $text, bool $eol = false): self
    {
        $this->writer->comment($text, $eol);

        return $this;
    }

    /**
     * Write an informational message.
     *
     * @param string $message Informational message
     * @param bool   $badge   Whether to show badge
     * @param string $label   Badge label
     *
     * @return self
     */
    public function info(string $message, bool $badge = true, string $label = 'INFO'): self
    {
        if (!$badge) {
            $this->writer->infoBold($label);
        } else {
            $this->writer->boldWhiteBgCyan(" {$label} ");
        }

        return $this->write(' ' . $message, true);
    }

    /**
     * Write a success message.
     *
     * @param string $message Success message
     * @param bool   $badge   Whether to show badge
     * @param string $label   Badge label
     *
     * @return self
     */
    public function success(string $message, bool $badge = true, string $label = 'SUCCESS'): self
    {
        if (!$badge) {
            $this->writer->okBold($label);
        } else {
            $this->writer->boldWhiteBgGreen(" {$label} ");
        }

        return $this->write(' ' . $message, true);
    }

    /**
     * Write a warning message.
     *
     * @param string $message Warning message
     * @param bool   $badge   Whether to show badge
     * @param string $label   Badge label
     *
     * @return self
     */
    public function warning(string $message, bool $badge = true, string $label = 'WARNING'): self
    {
        if (!$badge) {
            $this->writer->warnBold($label);
        } else {
            $this->writer->boldWhiteBgYellow(" {$label} ");
        }

        return $this->write(' ' . $message, true);
    }

    /**
     * Write an error message.
     *
     * @param string $message Error message
     * @param bool   $badge   Whether to show badge
     * @param string $label   Badge label
     *
     * @return self
     */
    public function error(string $message, bool $badge = true, string $label = 'ERROR'): self
    {
        if (!$badge) {
            $this->writer->errorBold($label);
        } else {
            $this->writer->boldWhiteBgRed(" {$label} ");
        }

        return $this->write(' ' . $message, true);
    }

    /**
     * Display an alert message.
     *
     * @param string $message Alert message
     * @param string $color   Alert color
     *
     * @return self
     */
    public function alert(string $message, string $color = 'yellow'): self
    {
        $this->newLine();
        $this->colorize(str_repeat('*', \strlen($message) + 12), $color);
        $this->colorize('*     ' . $message . '     *', $color);
        $this->colorize(str_repeat('*', \strlen($message) + 12), $color);
        $this->newLine();

        return $this;
    }

    /**
     * Display a bullet list.
     *
     * @param array<string> $items List items
     * @param string        $title List title
     * @param string        $color Title color
     *
     * @return self
     */
    public function bulletList(array $items, string $title = '', string $color = 'yellow'): self
    {
        if ($title !== '') {
            $this->colorize($title, $color);
        }

        foreach ($items as $item) {
            $this->write("  • {$item}")->eol();
        }

        return $this;
    }

    /**
     * Display a numbered list.
     *
     * @param array<string> $items List items
     * @param string        $title List title
     * @param string        $color Title color
     *
     * @return self
     */
    public function numberedList(array $items, string $title = '', string $color = 'yellow'): self
    {
        if ($title !== '') {
            $this->colorize($title, $color);
        }

        foreach ($items as $index => $item) {
            $this->writer->colors(sprintf("  <green>%d.</end> %s", $index + 1, $item))->eol();
        }

        return $this;
    }

    /**
     * Write a message with specific color.
     *
     * @param string $message Message to colorize
     * @param string $color   Color name
     *
     * @return self
     */
    public function colorize(string $message, string $color): self
    {
        $this->writer->colors('<' . $color . '>' . $message . '</end><eol>');

        return $this;
    }

    /**
     * Write an OK message.
     *
     * @param string $message OK message
     * @param bool   $eol     Whether to add end of line
     *
     * @return self
     */
    public function ok(string $message, bool $eol = false): self
    {
        $this->writer->ok($message, $eol);

        return $this;
    }

    /**
     * Write a fail message.
     *
     * @param string $message Fail message
     * @param bool   $eol     Whether to add end of line
     *
     * @return self
     */
    public function fail(string $message, bool $eol = false): self
    {
        $this->writer->error($message, $eol);

        return $this;
    }

    /**
     * Display currently executing task.
     *
     * @param string    $task  Task description
     * @param int|null  $sleep Sleep duration in seconds
     *
     * @return self
     */
    public function task(string $task, ?int $sleep = null): self
    {
        $this->write('>> ' . $task, true);

        if ($sleep !== null) {
            sleep($sleep);
        }

        return $this;
    }

    /**
     * Display a counter with animation.
     *
     * @param int $start Counter start value
     * @param int $end   Counter end value
     * @param int $step  Counter step value
     */
    public function counter(int $start = 0, int $end = 100, int $step = 1): void
    {
        for ($i = $start; $i <= $end; $i += $step) {
            $this->write($this->cursor->col(-4))->write(sprintf('%3d%%', $i));
            usleep(50000);
        }

        $this->eol();
    }

    /**
     * Display a table.
     *
     * @param array<array<string, mixed>> $rows   Table rows
     * @param array<string, mixed>        $styles Table styles
     *
     * @return self
     */
    public function table(array $rows, array $styles = []): self
    {
        $this->writer->table($rows, $styles);

        return $this;
    }

    /**
     * Display JSON formatted data.
     *
     * @param mixed $data Data to display as JSON
     *
     * @return self
     */
    public function json($data): self
    {
        $this->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), true);

        return $this;
    }

    /**
     * Display a border line.
     *
     * @param int|null $length Border length
     * @param string   $char   Border character
     *
     * @return self
     */
    public function border(?int $length = null, string $char = '-'): self
    {
        $length = $length ?: ($this->terminal->width() ?: 100);
        $str    = str_repeat($char, $length);
        $str    = substr($str, 0, $length);

        return $this->comment($str, true);
    }

    /**
     * Write text with tabs.
     *
     * @param int $repeat Number of tabs
     *
     * @return self
     */
    public function tab(int $repeat = 1): self
    {
        $this->write(str_repeat("\t", $repeat));

        return $this;
    }

    /**
     * Write text at the center of console.
     *
     * @param string              $text    Text to center
     * @param array<string, mixed> $options Center options
     *
     * @return self
     */
    public function center(string $text, array $options = []): self
    {
        $sep = $options['sep'] ?? ' ';
        unset($options['sep']);

        $dashWidth = ($this->terminal->width() ?: 100) - \strlen($text);
        $dashWidth -= 2;
        $dashWidth = (int) ($dashWidth / 2);

        $text     = $this->color->line($text, $options);
        $repeater = str_repeat($sep, $dashWidth);

        return $this->write($repeater . ' ' . $text . ' ' . $repeater)->eol();
    }

    /**
     * Write justified text (left and right aligned).
     *
     * @param string              $first   Left text
     * @param string|null         $second  Right text
     * @param array<string, mixed> $options Justify options
     *
     * @return self
     */
    public function justify(string $first, ?string $second = '', array $options = []): self
    {
        $this->writer->justify($first, $second, $options);

        return $this;
    }

    /**
     * Initialize a progress bar.
     *
     * @param int|null $total Total steps
     *
     * @return ProgressBar Progress bar instance
     */
    public function progress(?int $total = null): ProgressBar
    {
        return new ProgressBar($total, $this->writer);
    }

    /**
     * Add end of line(s).
     *
     * @param int $n Number of end of lines
     *
     * @return static
     */
    public function eol(int $n = 1): static
    {
        $this->writer->eol($n);

        return $this;
    }

    /**
     * Add a new empty line.
     *
     * @return self
     */
    public function newLine(): self
    {
        return $this->eol(1);
    }

    /**
     * Write text to output.
     *
     * @param string $text Text to write
     * @param bool   $eol  Whether to add end of line
     *
     * @return self
     */
    public function write(string $text, bool $eol = false): self
    {
        $this->writer->write($text, $eol);

        return $this;
    }

    /**
     * Clear the screen of output.
     */
    public function clearScreen(): void
    {
        // Unix systems, and Windows with VT100 Terminal support (i.e. Win10) can handle CSI sequences.
        // For lower than Win10 we just shove in 40 new lines.
        if ($this->terminal->isWindows() && (\function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT))) {
            $this->eol(40);
        } else {
            $this->writer->raw("\033[H\033[2J");
        }
    }
}
