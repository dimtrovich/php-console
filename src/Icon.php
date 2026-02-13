<?php

/**
 * This file is part of Blitz PHP - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\Console;

/**
 * Central registry of icon characters for console components.
 *
 * This class provides a centralized collection of Unicode icon characters
 * that can be used consistently across all console components like
 * alerts and badges.
 *
 * @example
 * ```php
 * use Dimtrovich\Console\Icon;
 *
 * $badge->success('User created', 'USER', Icon::USER);
 * $alert->warning('Low disk space', 'DISK', Icon::WARNING);
 * ```
 */
class Icon
{
    public const INFO      = 'ℹ';
    public const SUCCESS   = '✓';
    public const WARNING   = '⚠';
    public const ERROR     = '✗';
    public const DANGER    = '✘';
    public const PRIMARY   = '★';
    public const SECONDARY = '●';
    public const DARK      = '⬤';
    public const LIGHT     = '○';
    public const STAR      = '★';
    public const CHECK     = '✓';
    public const CROSS     = '✗';
    public const BULLET    = '•';
    public const ARROW     = '→';
    public const LOCK      = '🔒';
    public const KEY       = '🔑';
    public const TIME      = '⏱';
    public const DATABASE  = '🗄';
    public const CACHE     = '⚡';
    public const USER      = '👤';
    public const GROUP     = '👥';
    public const FILE      = '📄';
    public const FOLDER    = '📁';
    public const DOWNLOAD  = '⬇';
    public const UPLOAD    = '⬆';
    public const REFRESH   = '↻';
    public const SEARCH    = '🔍';
    public const HEART     = '❤';
    public const FLAG      = '⚐';
    public const WRENCH    = '🔧';
    public const GEAR      = '⚙';
    public const TRASH     = '🗑';
    public const MAIL      = '✉';
    public const CLOCK     = '🕐';
    public const CALENDAR  = '📅';
    public const CHART     = '📊';
    public const MUSIC     = '♪';
    public const BELL      = '🔔';
    public const BELL_OFF  = '🔕';
    public const BOOKMARK  = '🔖';
    public const TAG       = '🏷';
    public const PIN       = '📌';
    public const LINK      = '🔗';
}
