<?php

use Ahc\Cli\Output\Color;

/**
 * Monokai theme - popular syntax highlighting theme.
 * Vibrant colors on dark background.
 */
return [
    // ========================================================================
    // Monokai palette
    // ========================================================================
    'yellow'     => ['fg' => Color::fg256(221)], // #f1fa8c
    'red'        => ['fg' => Color::fg256(197)], // #f92672
    'blue'       => ['fg' => Color::fg256(81)],  // #66d9ef
    'cyan'       => ['fg' => Color::fg256(117)], // #a1efe4
    'green'      => ['fg' => Color::fg256(119)], // #a6e22e

    // ========================================================================
    // Built-in adhocore/cli styles mapped to Monokai
    // ========================================================================

    // Help screen styles
    'help_header'           => ['fg' => Color::fg256(117)], // cyan
    'help_item_even'        => ['fg' => Color::fg256(117)], // cyan
    'help_item_odd'         => ['fg' => Color::fg256(117)], // cyan
    'help_group'            => ['fg' => Color::fg256(119), 'bold' => 1], // green
    'help_category'         => ['fg' => Color::fg256(221)], // yellow
    'help_usage'            => ['fg' => Color::fg256(141)], // purple
    'help_description_even' => ['fg' => Color::fg256(249)], // foreground
    'help_description_odd'  => ['fg' => Color::fg256(249)], // foreground
    'help_summary'          => ['fg' => Color::fg256(249)], // foreground
    'help_example'          => ['fg' => Color::GRAY],
    'help_text'             => ['fg' => Color::fg256(249)], // foreground
    'help_footer'           => ['fg' => Color::GRAY],

    // Input/Output styles
    'answer'                => ['fg' => Color::fg256(117), 'bold' => 1], // cyan
    'choice'                => ['fg' => Color::fg256(119), 'bold' => 1], // green
    'comment'               => ['fg' => Color::fg256(141)], // purple
    'error'                 => ['fg' => Color::fg256(197), 'bold' => 1], // red
    'info'                  => ['fg' => Color::fg256(81)], // blue
    'logo'                  => ['fg' => Color::fg256(205)], // pink
    'ok'                    => ['fg' => Color::fg256(119)], // green
    'question'              => ['fg' => Color::fg256(221)], // yellow
    'version'               => ['fg' => Color::fg256(119)], // green
    'warn'                  => ['fg' => Color::fg256(215)], // orange

    // ========================================================================
    // BlitzPHP custom styles
    // ========================================================================

    // Extended colors
    'magenta'               => ['fg' => Color::fg256(205)], // pink
    'indigo'                => ['fg' => Color::fg256(141)], // purple
    'purple'                => ['fg' => Color::fg256(141)], // purple
    'orange'                => ['fg' => Color::fg256(215)], // orange
    'pink'                  => ['fg' => Color::fg256(205)], // pink
    'brown'                 => ['fg' => Color::fg256(130)],
];
