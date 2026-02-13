<?php

/**
 * This file is part of Dimtrovich - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\Console\Components;

trait SingletonTrait
{
    /**
     * Singleton instance.
     *
     * @var static|null
     */
    private static $instance;

    /**
     * Get the singleton instance.
     */
    public static function instance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static(...func_get_args());
        }

        return static::$instance;
    }

    public static function resetInstance()
    {
        static::$instance = null;
    }
}
