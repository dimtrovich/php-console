<?php

declare(strict_types=1);

namespace BlitzPHP\Console\Components;

use Ahc\Cli\Output\Writer;
use Exception;

/**
 * Badge component for console output.
 *
 * @package BlitzPHP\Console\Components
 */
class Badge
{

    /**
     * Writer instance.
     */
    private Writer $writer;

	/**
	 * Singleton instance.
	 */
	private static ?self $instance = null;

    /**
     * Create a new alert instance.
     *
     * @param Writer $writer Writer instance
     */
    public function __construct(Writer $writer)
    {
        $this->writer = $writer;
    }

	/**
	 * Get the singleton instance of Alert.
	 */
	public static function instance(Writer $writer): static
	{
		if (self::$instance === null) {
			self::$instance = new static($writer);
		}

		return self::$instance;
	}

    /**
     * Display an info badge.
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     *
     * @return self
     */
    public function info(string $message, string $label = 'INFO'): self
    {
        $this->writer->boldWhiteBgCyan(" {$label} ");
        $this->writer->write(' ' . $message, true);

        return $this;
    }

    /**
     * Display a success badge.
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     *
     * @return self
     */
    public function success(string $message, string $label = 'SUCCESS'): self
    {
        $this->writer->boldWhiteBgGreen(" {$label} ");
        $this->writer->write(' ' . $message, true);

        return $this;
    }

    /**
     * Display a warning badge.
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     *
     * @return self
     */
    public function warning(string $message, string $label = 'WARNING'): self
    {
        $this->writer->boldWhiteBgYellow(" {$label} ");
        $this->writer->write(' ' . $message, true);

        return $this;
    }

    /**
     * Display an error badge.
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     *
     * @return self
     */
    public function error(string $message, string $label = 'ERROR'): self
    {
        $this->writer->boldWhiteBgRed(" {$label} ");
        $this->writer->write(' ' . $message, true);

        return $this;
    }

    /**
     * Display a danger badge (alias for error).
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     *
     * @return self
     */
    public function danger(string $message, string $label = 'DANGER'): self
    {
        return $this->error($message, $label);
    }

    /**
     * Display a primary badge.
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     *
     * @return self
     */
    public function primary(string $message, string $label = 'PRIMARY'): self
    {
        $this->writer->boldWhiteBgBlue(" {$label} ");
        $this->writer->write(' ' . $message, true);

        return $this;
    }

    /**
     * Display a secondary badge.
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     *
     * @return self
     */
    public function secondary(string $message, string $label = 'SECONDARY'): self
    {
        $this->writer->boldWhiteBgGray(" {$label} ");
        $this->writer->write(' ' . $message, true);

        return $this;
    }

    /**
     * Display a dark badge.
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     *
     * @return self
     */
    public function dark(string $message, string $label = 'DARK'): self
    {
        $this->writer->boldWhiteBgBlack(" {$label} ");
        $this->writer->write(' ' . $message, true);

        return $this;
    }

    /**
     * Display a light badge.
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     *
     * @return self
     */
    public function light(string $message, string $label = 'LIGHT'): self
    {
        $this->writer->boldBlackBgWhite(" {$label} ");
        $this->writer->write(' ' . $message, true);

        return $this;
    }

    /**
     * Display an outline badge.
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     * @param string $color   Badge color
     *
     * @return self
     */
    public function outline(string $message, string $label = 'OUTLINE', string $color = 'blue'): self
    {
        $method = match ($color) {
            'info'      => 'boldCyan',
            'success'   => 'boldGreen',
            'warning'   => 'boldYellow',
            'error'     => 'boldRed',
            'danger'    => 'boldRed',
            'primary'   => 'boldBlue',
            'secondary' => 'boldGray',
            'dark'      => 'boldBlack',
            'light'     => 'boldWhite',
            default     => 'bold' . ucfirst(strtolower($color)),
        };

        try {
			$this->writer->{$method}(" {$label} ");
		} catch (Exception) {
			$this->writer->bold(" {$label} ");
		}

        $this->writer->write(' ' . $message)->eol();

        return $this;
    }

    /**
     * Display a pill badge (rounded corners).
     *
     * @param string $message Badge message
     * @param string $label   Badge label
     * @param string $color   Badge color
     *
     * @return self
     */
    public function pill(string $message, string $label = 'PILL', string $color = 'blue'): self
    {
        $method = match ($color) {
            'info'      => 'boldWhiteBgCyan',
            'success'   => 'boldWhiteBgGreen',
            'warning'   => 'boldWhiteBgYellow',
            'error'     => 'boldWhiteBgRed',
            'danger'    => 'boldWhiteBgRed',
            'primary'   => 'boldWhiteBgBlue',
            'secondary' => 'boldWhiteBgGray',
            'dark'      => 'boldWhiteBgBlack',
            'light'     => 'boldBlackBgWhite',
            default     => 'boldWhiteBgBlue',
        };

        // Add rounded brackets for pill effect
        $this->writer->{$method}("( {$label} )");
        $this->writer->write(' ' . $message, true);

        return $this;
    }
}
