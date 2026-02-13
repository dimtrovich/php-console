<?php

namespace Dimtrovich\Console\Components;

trait SingletonTrait
{

    /**
     * Singleton instance.
	 *
	 * @var static|null
     */
    private static $instance = null;

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
