<?php

namespace Dimtrovich\Console\Components;

/**
 * Trait for components that support icons.
 *
 * Provides consistent icon display functionality across
 * Alert, Badge, Logger, and other components.
 *
 * @package Dimtrovich\Console\Components
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
     * Resolve the icon based on input and global settings.
     *
     * @param string|null|false $icon     Icon parameter from method call
     * @param string|null       $default  Default icon for this alert type
     *
     * @return string|null The resolved icon (null = no icon)
     */
    protected function resolveIcon(string|null|false $icon, ?string $default): ?string
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
}
