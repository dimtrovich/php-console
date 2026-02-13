<?php

use Ahc\Cli\Output\Color;

/**
 * Solarized theme - Ethan Schoonover's popular color scheme.
 * Perfect for long coding sessions.
 */
return [
    // ========================================================================
    // Solarized palette
    // ========================================================================
    'yellow'  => ['fg' => Color::fg256(136)],
    'orange'  => ['fg' => Color::fg256(166)],
    'red'     => ['fg' => Color::fg256(160)],
    'magenta' => ['fg' => Color::fg256(125)],
    'violet'  => ['fg' => Color::fg256(61)],
    'blue'    => ['fg' => Color::fg256(33)],
    'cyan'    => ['fg' => Color::fg256(37)],
    'green'   => ['fg' => Color::fg256(64)],

    // ========================================================================
    // Built-in adhocore/cli styles mapped to Solarized
    // ========================================================================

    // Help screen styles
    'help_header'           => ['fg' => Color::CYAN],
    'help_item_even'        => ['fg' => Color::CYAN],
    'help_item_odd'         => ['fg' => Color::CYAN],
    'help_group'            => ['fg' => Color::GREEN, 'bold' => 1],
    'help_category'         => ['fg' => Color::YELLOW],
    'help_usage'            => ['fg' => Color::BLUE],
    'help_description_even' => ['fg' => Color::fg256(244)], // base0
    'help_description_odd'  => ['fg' => Color::fg256(245)], // base1
    'help_summary'          => ['fg' => Color::fg256(244)], // base0
    'help_example'          => ['fg' => Color::fg256(240)], // base01
    'help_text'             => ['fg' => Color::fg256(244)], // base0
    'help_footer'           => ['fg' => Color::fg256(240)], // base01

    // Input/Output styles
    'answer'                => ['fg' => Color::CYAN, 'bold' => 1],
    'choice'                => ['fg' => Color::GREEN, 'bold' => 1],
    'comment'               => ['fg' => Color::YELLOW],
    'error'                 => ['fg' => Color::RED, 'bold' => 1],
    'info'                  => ['fg' => Color::BLUE],
    'logo'                  => ['fg' => Color::PURPLE],
    'ok'                    => ['fg' => Color::GREEN],
    'question'              => ['fg' => Color::YELLOW],
    'version'               => ['fg' => Color::GREEN],
    'warn'                  => ['fg' => Color::fg256(166)], // orange

    // ========================================================================
    // BlitzPHP custom styles
    // ========================================================================

    // Extended colors using fg256
    'indigo'                => ['fg' => Color::fg256(61)],
    'purple'                => ['fg' => Color::fg256(61)],
    'pink'                  => ['fg' => Color::fg256(168)],
    'brown'                 => ['fg' => Color::fg256(130)],
];
