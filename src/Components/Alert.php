<?php

declare(strict_types=1);

namespace BlitzPHP\Console\Components;

use Ahc\Cli\Output\Writer;

/**
 * Alert component for console output.
 *
 * @package BlitzPHP\Console\Components
 */
class Alert
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
     * Display an info alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     *
     * @return self
     */
    public function info(string $message, ?string $title = null): self
    {
        return $this->render($message, 'info', 'INFO', $title);
    }

    /**
     * Display a success alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     *
     * @return self
     */
    public function success(string $message, ?string $title = null): self
    {
        return $this->render($message, 'success', 'SUCCESS', $title);
    }

    /**
     * Display a warning alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     *
     * @return self
     */
    public function warning(string $message, ?string $title = null): self
    {
        return $this->render($message, 'warning', 'WARNING', $title);
    }

    /**
     * Display an error alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     *
     * @return self
     */
    public function error(string $message, ?string $title = null): self
    {
        return $this->render($message, 'error', 'ERROR', $title);
    }

    /**
     * Display a danger alert (alias for error).
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     *
     * @return self
     */
    public function danger(string $message, ?string $title = null): self
    {
        return $this->error($message, $title);
    }

    /**
     * Display a primary alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     *
     * @return self
     */
    public function primary(string $message, ?string $title = null): self
    {
        return $this->render($message, 'primary', 'ALERT', $title);
    }

    /**
     * Display a secondary alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     *
     * @return self
     */
    public function secondary(string $message, ?string $title = null): self
    {
        return $this->render($message, 'secondary', 'NOTE', $title);
    }

    /**
     * Display a dark alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     *
     * @return self
     */
    public function dark(string $message, ?string $title = null): self
    {
        return $this->render($message, 'dark', 'ALERT', $title);
    }

    /**
     * Display a light alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     *
     * @return self
     */
    public function light(string $message, ?string $title = null): self
    {
        return $this->render($message, 'light', 'NOTE', $title);
    }

    /**
     * Render the alert.
     *
     * @param string      $message Alert message
     * @param string      $type    Alert type
     * @param string      $defaultTitle Default title
     * @param string|null $title   Custom title
     *
     * @return self
     */
    private function render(string $message, string $type, string $defaultTitle, ?string $title = null): self
    {
        $title = $title ?? $defaultTitle;

        $this->writer->newLine();

        // Top border
        $this->renderBorder($message, $type);

        // Title line
        $this->renderTitle($title, $type);

        // Message line
        $this->renderMessage($message, $type);

        // Bottom border
        $this->renderBorder($message, $type);

        $this->writer->newLine();

        return $this;
    }

    /**
     * Render alert border.
     *
     * @param string $message Message for length calculation
     * @param string $type    Alert type
     */
    private function renderBorder(string $message, string $type): void
    {
        $length = max(strlen($message), \strlen($type) + 2) + 12;
        $border = str_repeat('*', $length);

        $this->writer->colors('<' . $this->getBorderColor($type) . '>' . $border . '</end>')->eol();
    }

    /**
     * Render alert title.
     *
     * @param string $title Alert title
     * @param string $type  Alert type
     */
    private function renderTitle(string $title, string $type): void
    {
        $padding = 6;
        $formattedTitle = str_pad('*  ' . $title . '  *', $padding * 2 + \strlen($title) + 4, ' ', STR_PAD_BOTH);

        $this->writer->colors('<' . $this->getTitleColor($type) . '>' . $formattedTitle . '</end>')->eol();
    }

    /**
     * Render alert message.
     *
     * @param string $message Alert message
     * @param string $type    Alert type
     */
    private function renderMessage(string $message, string $type): void
    {
        $lines = explode("\n", wordwrap($message, 60, "\n", true));

        foreach ($lines as $line) {
            $paddedLine = str_pad('*  ' . $line . '  *', \strlen($line) + 10, ' ', STR_PAD_RIGHT);
            $this->writer->colors('<' . $this->getMessageColor($type) . '>' . $paddedLine . '</end>')->eol();
        }
    }

    /**
     * Get border color for alert type.
     *
     * @param string $type Alert type
     *
     * @return string Color name
     */
    private function getBorderColor(string $type): string
    {
        return match ($type) {
            'info'      => 'cyan',
            'success'   => 'green',
            'warning'   => 'yellow',
            'error'     => 'red',
            'danger'    => 'red',
            'primary'   => 'blue',
            'secondary' => 'gray',
            'dark'      => 'black',
            'light'     => 'white',
            default     => 'white',
        };
    }

    /**
     * Get title color for alert type.
     *
     * @param string $type Alert type
     *
     * @return string Color name
     */
    private function getTitleColor(string $type): string
    {
        return match ($type) {
            'info'      => 'boldCyan',
            'success'   => 'boldGreen',
            'warning'   => 'boldYellow',
            'error'     => 'boldRed',
            'danger'    => 'boldRed',
            'primary'   => 'boldBlue',
            'secondary' => 'boldGray',
            'dark'      => 'boldWhite',
            'light'     => 'boldBlack',
            default     => 'boldWhite',
        };
    }

    /**
     * Get message color for alert type.
     *
     * @param string $type Alert type
     *
     * @return string Color name
     */
    private function getMessageColor(string $type): string
    {
        return match ($type) {
            'info'      => 'cyan',
            'success'   => 'green',
            'warning'   => 'yellow',
            'error'     => 'red',
            'danger'    => 'red',
            'primary'   => 'blue',
            'secondary' => 'gray',
            'dark'      => 'white',
            'light'     => 'black',
            default     => 'white',
        };
    }
}
