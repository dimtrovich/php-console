<?php

/**
 * This file is part of Blitz PHP - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Helpers;

use php_user_filter;
use ReturnTypeWillChange;

/**
 * Helper to test console output
 *
 * @credit <a href="https://github.com/adhocore/php-cli/blob/main/tests/CliTestCase.php">PHP-CLI by Jitendra Adhikari/a>
 */
class ConsoleOutput
{
    protected static $ou = __DIR__ . '/../../output.test';

    public static function setUpBeforeClass(): void
    {
        if (! is_dir($dirname = pathinfo(static::$ou, PATHINFO_DIRNAME))) {
            mkdir($dirname, 0o777, true);
        }

        // Thanks: https://stackoverflow.com/a/39785995
        stream_filter_register('intercept', StreamInterceptor::class);
        stream_filter_append(\STDOUT, 'intercept');
        stream_filter_append(\STDERR, 'intercept');
    }

    public static function setUp(): void
    {
        ob_start();
        StreamInterceptor::$buffer = '';
        file_put_contents(static::$ou, '', LOCK_EX);
    }

    public static function tearDown(): void
    {
        ob_end_clean();
    }

    public static function tearDownAfterClass(): void
    {
        // Make sure we clean up after ourselves:
        if (file_exists(static::$ou)) {
            unlink(static::$ou);
        }
    }

    public static function buffer()
    {
        return StreamInterceptor::$buffer ?: file_get_contents(static::$ou);
    }
}

class StreamInterceptor extends php_user_filter
{
    public static $buffer = '';

    #[ReturnTypeWillChange]
    public function filter($in, $out, &$consumed, $closing): int
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            static::$buffer .= $bucket->data;
        }

        return PSFS_PASS_ON;
    }
}
