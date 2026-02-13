<?php

declare(strict_types=1);

namespace BlitzPHP\Console\Components;

use Ahc\Cli\Output\Writer;
use BlitzPHP\Console\Icon;
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
     * Whether to show default icons.
     */
    private static bool $showDefaultIcons = false;

    /**
     * Create a new badge instance.
     *
     * @param Writer $writer Writer instance
     */
    public function __construct(Writer $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Get the singleton instance of Badge.
     */
    public static function instance(Writer $writer): static
    {
        if (self::$instance === null) {
            self::$instance = new static($writer);
        }

        return self::$instance;
    }

    /**
     * Enable or disable default icons globally.
     *
     * @param bool $enabled Whether to show default icons
     *
     * @return void
     *
     * @example
     * ```php
     * Badge::showDefaultIcons(false); // Disable all default icons
     * Badge::showDefaultIcons(true);  // Re-enable default icons
     * ```
     */
    public static function showDefaultIcons(bool $enabled): void
    {
        self::$showDefaultIcons = $enabled;
    }

    /**
     * Check if default icons are enabled.
     *
     * @return bool
     */
    public static function defaultIconsEnabled(): bool
    {
        return self::$showDefaultIcons;
    }

    /**
     * Display an info badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function info(string $message, string $label = 'INFO', string|null|false $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::INFO);
        return $this->render($message, $label, 'boldWhiteBgCyan', $resolvedIcon);
    }

    /**
     * Display a success badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function success(string $message, string $label = 'SUCCESS', string|null|false $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::SUCCESS);
        return $this->render($message, $label, 'boldWhiteBgGreen', $resolvedIcon);
    }

    /**
     * Display a warning badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function warning(string $message, string $label = 'WARNING', string|null|false $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::WARNING);
        return $this->render($message, $label, 'boldWhiteBgYellow', $resolvedIcon);
    }

    /**
     * Display an error badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function error(string $message, string $label = 'ERROR', string|null|false $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::ERROR);
        return $this->render($message, $label, 'boldWhiteBgRed', $resolvedIcon);
    }

    /**
     * Display a danger badge (alias for error).
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function danger(string $message, string $label = 'DANGER', string|null|false $icon = null): self
    {
        return $this->error($message, $label, $icon);
    }

    /**
     * Display a primary badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function primary(string $message, string $label = 'PRIMARY', string|null|false $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::PRIMARY);
        return $this->render($message, $label, 'boldWhiteBgBlue', $resolvedIcon);
    }

    /**
     * Display a secondary badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function secondary(string $message, string $label = 'SECONDARY', string|null|false $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::SECONDARY);
        return $this->render($message, $label, 'boldWhiteBgGray', $resolvedIcon);
    }

    /**
     * Display a dark badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function dark(string $message, string $label = 'DARK', string|null|false $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::DARK);
        return $this->render($message, $label, 'boldWhiteBgBlack', $resolvedIcon);
    }

    /**
     * Display a light badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function light(string $message, string $label = 'LIGHT', string|null|false $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, Icon::LIGHT);
        return $this->render($message, $label, 'boldBlackBgWhite', $resolvedIcon);
    }

    /**
     * Display an outline badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string      $color   Badge color
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function outline(string $message, string $label = 'OUTLINE', string $color = 'blue', string|null|false $icon = null): self
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

        $defaultIcon  = $this->getColorDefaultIcon($color);
        $resolvedIcon = $this->resolveIcon($icon, $defaultIcon);
        $iconPart     = $resolvedIcon ? $resolvedIcon . ' ' : '';

        try {
            $this->writer->{$method}(" {$iconPart}{$label} ");
        } catch (Exception) {
            $this->writer->bold(" {$iconPart}{$label} ");
        }

        $this->writer->write(' ' . $message)->eol();

        return $this;
    }

    /**
     * Display a pill badge (rounded corners).
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string      $color   Badge color
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function pill(string $message, string $label = 'PILL', string $color = 'blue', string|null|false $icon = null): self
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

        $defaultIcon  = $this->getColorDefaultIcon($color);
        $resolvedIcon = $this->resolveIcon($icon, $defaultIcon);
        $iconPart     = $resolvedIcon ? $resolvedIcon . ' ' : '';

        // Add rounded brackets for pill effect
        $this->writer->{$method}("( {$iconPart}{$label} )");
        $this->writer->write(' ' . $message, true);

        return $this;
    }

    /**
     * Display a badge with custom style.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string      $style   Writer method to use for styling
     * @param string|null $icon    Optional icon to display before the label
     *                             (null = use default if enabled, false = no icon)
     *
     * @return self
     */
    public function custom(string $message, string $label, string $style, string|null|false $icon = null): self
    {
        $resolvedIcon = $this->resolveIcon($icon, null);
        return $this->render($message, $label, $style, $resolvedIcon);
    }

    /**
     * Resolve the icon based on input and global settings.
     *
     * @param string|null|false $icon     Icon parameter from method call
     * @param string|null       $default  Default icon for this badge type
     *
     * @return string|null The resolved icon (null = no icon)
     */
    private function resolveIcon(string|null|false $icon, ?string $default): ?string
    {
        // Explicitly false means no icon, regardless of global setting
        if ($icon === false) {
            return null;
        }

        // Explicitly provided icon
        if (is_string($icon)) {
            return $icon;
        }

        // Null means use default if globally enabled
        if ($icon === null && self::$showDefaultIcons) {
            return $default;
        }

        // No icon
        return null;
    }

    /**
     * Render a badge.
     *
     * @param string      $message Badge message
     * @param string      $label   Badge label
     * @param string      $style   Writer method to use for styling
     * @param string|null $icon    Resolved icon (null = no icon)
     *
     * @return self
     */
    private function render(string $message, string $label, string $style, ?string $icon): self
    {
        $iconPart = $icon ? $icon . ' ' : '';

        try {
            $this->writer->{$style}(" {$iconPart}{$label} ");
        } catch (Exception) {
            // Fallback to bold if style method doesn't exist
            $this->writer->bold(" {$iconPart}{$label} ");
        }

        $this->writer->write(' ' . $message, true);

        return $this;
    }

	private function getColorDefaultIcon(string $color): ?string
	{
		return match ($color) {
            'info'      => Icon::INFO,
            'success'   => Icon::SUCCESS,
            'warning'   => Icon::WARNING,
            'error'     => Icon::ERROR,
            'danger'    => Icon::DANGER,
            'primary'   => Icon::PRIMARY,
            'secondary' => Icon::SECONDARY,
            'dark'      => Icon::DARK,
            'light'     => Icon::LIGHT,
            default     => null,
        };
	}
}
