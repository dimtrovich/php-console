<?php

declare(strict_types=1);

/**
 * This file is part of Dimtrovich - Console.
 *
 * (c) 2026 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\Console\Overrides;

use Ahc\Cli\Output\Cursor as AhcCursor;

/**
 * Extended cursor with additional functionality.
 */
class Cursor extends AhcCursor
{
    /**
     * Hide the cursor.
     *
     * @return string ANSI escape sequence
     */
    public function hide(): string
    {
        return "\e[?25l";
    }

    /**
     * Show the cursor.
     *
     * @return string ANSI escape sequence
     */
    public function show(): string
    {
        return "\e[?25h";
    }

    /**
     * Move cursor to a specific column.
     *
     * @param int $col Column position (negative values move left)
     *
     * @return string ANSI escape sequence
     */
    public function col(int $col): string
    {
        return $col >= 0 ? $this->right($col) : $this->left(abs($col));
    }

    /**
     * Position cursor at specific row and column.
     *
     * @param int $row Row position
     * @param int $col Column position
     *
     * @return string ANSI escape sequence
     */
    public function position(int $row, int $col): string
    {
        return "\e[{$row};{$col}H";
    }

    /**
     * Save cursor position.
     *
     * @return string ANSI escape sequence
     */
    public function save(): string
    {
        return "\e[s";
    }

    /**
     * Restore cursor position.
     *
     * @return string ANSI escape sequence
     */
    public function restore(): string
    {
        return "\e[u";
    }
}
