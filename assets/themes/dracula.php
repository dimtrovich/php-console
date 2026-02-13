<?php

use Ahc\Cli\Output\Color;

/**
 * Dracula theme - dark theme with vibrant colors.
 * Popular among developers for its eye-catching palette.
 */
return [
    // ========================================================================
    // Dracula palette
    // ========================================================================
    'cyan'       => ['fg' => Color::fg256(117)], // #8be9fd
    'green'      => ['fg' => Color::fg256(84)],  // #50fa7b
    'red'        => ['fg' => Color::fg256(203)], // #ff5555
    'yellow'     => ['fg' => Color::fg256(227)], // #f1fa8c

    // ========================================================================
    // Built-in adhocore/cli styles mapped to Dracula
    // ========================================================================

    'help_header'           => ['fg' => Color::fg256(212)], // pink
    'help_item_even'        => ['fg' => Color::fg256(212)], // pink
    'help_item_odd'         => ['fg' => Color::fg256(141)], // purple
    'help_group'            => ['fg' => Color::fg256(84), 'bold' => 1], // green
    'help_category'         => ['fg' => Color::fg256(227)], // yellow
    'help_usage'            => ['fg' => Color::fg256(253)], // foreground
    'help_description_even' => ['fg' => Color::fg256(253)], // foreground
    'help_description_odd'  => ['fg' => Color::fg256(253)], // foreground
    'help_summary'          => ['fg' => Color::fg256(253)], // foreground
    'help_example'          => ['fg' => Color::fg256(242)], // comment
    'help_text'             => ['fg' => Color::fg256(253)], // foreground
    'help_footer'           => ['fg' => Color::fg256(242)], // comment

    'answer'                => ['fg' => Color::fg256(117), 'bold' => 1], // cyan
    'choice'                => ['fg' => Color::fg256(84), 'bold' => 1],  // green
    'comment'               => ['fg' => Color::fg256(242)],  // #6272a4
    'error'                 => ['fg' => Color::fg256(203), 'bold' => 1], // red
    'info'                  => ['fg' => Color::fg256(117)], // cyan
    'logo'                  => ['fg' => Color::fg256(212)], // pink
    'ok'                    => ['fg' => Color::fg256(84)], // green
    'question'              => ['fg' => Color::fg256(227)], // yellow
    'version'               => ['fg' => Color::fg256(84)], // green
    'warn'                  => ['fg' => Color::fg256(215)], // orange

    // ========================================================================
    // BlitzPHP custom styles
    // ========================================================================

    'magenta'               => ['fg' => Color::fg256(212)], // pink
    'indigo'                => ['fg' => Color::fg256(141)], // purple
    'purple'                => ['fg' => Color::fg256(141)], // purple
    'orange'                => ['fg' => Color::fg256(215)], // orange
    'pink'                  => ['fg' => Color::fg256(212)], // pink
    'brown'                 => ['fg' => Color::fg256(130)],
];
