<?php

namespace BlitzPHP\Console\Components;

/**
 * Trait for components that support icons.
 *
 * Provides consistent icon display functionality across
 * Alert, Badge, Logger, and other components.
 *
 * @package BlitzPHP\Console\Components
 */
trait IconTrait
{
    /**
     * Whether to show default icons globally.
     *
     * When true, components will display their default icons
     * (e.g., ℹ for info, ✓ for success) unless explicitly overridden.
     *
     * @var bool
     */
    private static bool $showDefaultIcons = false;

    /**
     * Enable or disable default icons globally.
     *
     * This setting affects all instances of components using this trait.
     * Individual method calls can still override with explicit icon parameters.
     *
     * @param bool $enabled Whether to show default icons
     *
     * @return void
     *
     * @example
     * ```php
     * // Disable all default icons
     * Alert::showDefaultIcons(false);
     * Badge::showDefaultIcons(false);
     * Logger::showDefaultIcons(false);
     *
     * // Re-enable default icons
     * Alert::showDefaultIcons(true);
     * ```
     */
    public static function showDefaultIcons(bool $enabled): void
    {
        self::$showDefaultIcons = $enabled;
    }

    /**
     * Check if default icons are enabled globally.
     *
     * @return bool True if default icons are enabled
     */
    public static function defaultIconsEnabled(): bool
    {
        return self::$showDefaultIcons;
    }

    /**
     * Determine if an icon should be displayed.
     *
     * @param string|null $icon     The explicitly provided icon (null if none)
     * @param string|null $default  The default icon for this component
     *
     * @return string|null The icon to display, or null for no icon
     */
    protected function resolveIcon(?string $icon, ?string $default): ?string
    {
        if ($icon !== null) {
            return $icon;
        }

        return self::$showDefaultIcons ? $default : null;
    }
}
