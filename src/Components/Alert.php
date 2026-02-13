<?php

declare(strict_types=1);

/**
 * This file is part of Blitz PHP - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\Console\Components;

use Ahc\Cli\Output\Writer;
use Dimtrovich\Console\Icon;

/**
 * Alert component for console output.
 */
class Alert
{
    use IconTrait;
    use SingletonTrait;

    /**
     * Writer instance.
     */
    private Writer $writer;

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
     * Display an info alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function info(string $message, ?string $title = null, false|string|null $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::INFO);

        return $this->render($message, 'info', $title ?? 'INFO', $resolvedIcon);
    }

    /**
     * Display a success alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function success(string $message, ?string $title = null, false|string|null $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::SUCCESS);

        return $this->render($message, 'success', $title ?? 'SUCCESS', $resolvedIcon);
    }

    /**
     * Display a warning alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function warning(string $message, ?string $title = null, false|string|null $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::WARNING);

        return $this->render($message, 'warning', $title ?? 'WARNING', $resolvedIcon);
    }

    /**
     * Display an error alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function error(string $message, ?string $title = null, false|string|null $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::ERROR);

        return $this->render($message, 'error', $title ?? 'ERROR', $resolvedIcon);
    }

    /**
     * Display a danger alert (alias for error).
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function danger(string $message, ?string $title = null, false|string|null $icon = null): self
    {
        return $this->error($message, $title, $icon);
    }

    /**
     * Display a primary alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function primary(string $message, ?string $title = null, false|string|null $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::PRIMARY);

        return $this->render($message, 'primary', $title ?? 'ALERT', $resolvedIcon);
    }

    /**
     * Display a secondary alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function secondary(string $message, ?string $title = null, false|string|null $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::SECONDARY);

        return $this->render($message, 'secondary', $title ?? 'NOTE', $resolvedIcon);
    }

    /**
     * Display a dark alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function dark(string $message, ?string $title = null, false|string|null $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::DARK);

        return $this->render($message, 'dark', $title ?? 'ALERT', $resolvedIcon);
    }

    /**
     * Display a light alert.
     *
     * @param string      $message Alert message
     * @param string|null $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function light(string $message, ?string $title = null, false|string|null $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::LIGHT);

        return $this->render($message, 'light', $title ?? 'NOTE', $resolvedIcon);
    }

    /**
     * Display a custom alert.
     *
     * @param string      $message Alert message
     * @param string      $type    Alert type for color scheme
     * @param string      $title   Alert title
     * @param string|null $icon    Optional icon to display before the title
     *                             (null = use default if enabled, false = no icon)
     */
    public function custom(string $message, string $type, string $title, false|string|null $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, null);

        return $this->render($message, $type, $title, $resolvedIcon);
    }

    /**
     * Render the alert.
     *
     * @param string      $message Alert message
     * @param string      $type    Alert type
     * @param string      $title   Alert title
     * @param string|null $icon    Resolved icon (null = no icon)
     */
    private function render(string $message, string $type, string $title, ?string $icon): self
    {
        $this->writer->eol();

        // Top border
        $this->renderBorder($message, $type, $title, $icon);

        // Title line with icon
        $this->renderTitle($title, $type, $icon);

        // Message line
        $this->renderMessage($message, $type);

        // Bottom border
        $this->renderBorder($message, $type, $title, $icon);

        $this->writer->eol();

        return $this;
    }

    /**
     * Render alert border.
     *
     * @param string      $message Message for length calculation
     * @param string      $type    Alert type
     * @param string      $title   Alert title
     * @param string|null $icon    Optional icon
     */
    private function renderBorder(string $message, string $type, string $title, ?string $icon): void
    {
        $iconLength  = $icon ? 2 : 0; // Icon + space
        $titleLength = strlen($title) + $iconLength;
        $length      = max(strlen($message), $titleLength + 2) + 12;
        $border      = str_repeat('*', $length);

        $this->writer->colors('<' . $this->getBorderColor($type) . '>' . $border . '</end>')->eol();
    }

    /**
     * Render alert title with optional icon.
     *
     * @param string      $title Alert title
     * @param string      $type  Alert type
     * @param string|null $icon  Optional icon
     */
    private function renderTitle(string $title, string $type, ?string $icon): void
    {
        $iconPart     = $icon ? $icon . ' ' : '';
        $displayTitle = $iconPart . $title;

        $padding        = 6;
        $formattedTitle = str_pad('*  ' . $displayTitle . '  *', $padding * 2 + strlen($displayTitle) + 4, ' ', STR_PAD_BOTH);

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
            $paddedLine = str_pad('*  ' . $line . '  *', strlen($line) + 10, ' ', STR_PAD_RIGHT);
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
