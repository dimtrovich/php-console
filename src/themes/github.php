<?php

use Ahc\Cli\Output\Color;

/**
 * GitHub theme - familiar colors from GitHub's interface.
 * Clean and professional, works well on light backgrounds.
 */
return [
    // ========================================================================
    // GitHub palette
    // ========================================================================
    'red'            => ['fg' => Color::fg256(196)], // #cb2431
    'green'          => ['fg' => Color::fg256(28)],  // #28a745
    'blue'           => ['fg' => Color::fg256(21)],  // #0366d6
    'yellow'         => ['fg' => Color::fg256(214)], // #ffd33d
    'gray'           => ['fg' => Color::fg256(243)], // #586069

    // ========================================================================
    // Built-in adhocore/cli styles mapped to GitHub
    // ========================================================================

    'help_header'           => ['fg' => Color::fg256(21)], // blue
    'help_item_even'        => ['fg' => Color::fg256(21)], // blue
    'help_item_odd'         => ['fg' => Color::fg256(21)], // blue
    'help_group'            => ['fg' => Color::fg256(28), 'bold' => 1], // green
    'help_category'         => ['fg' => Color::fg256(214)], // yellow
    'help_usage'            => ['fg' => Color::BLACK],
    'help_description_even' => ['fg' => Color::BLACK],
    'help_description_odd'  => ['fg' => Color::GRAY],
    'help_summary'          => ['fg' => Color::BLACK],
    'help_example'          => ['fg' => Color::GRAY],
    'help_text'             => ['fg' => Color::BLACK],
    'help_footer'           => ['fg' => Color::GRAY],

    'answer'                => ['fg' => Color::fg256(21), 'bold' => 1], // blue
    'choice'                => ['fg' => Color::fg256(28), 'bold' => 1], // green
    'comment'               => ['fg' => Color::fg256(92)], // purple
    'error'                 => ['fg' => Color::fg256(196), 'bold' => 1], // red
    'info'                  => ['fg' => Color::fg256(21)], // blue
    'logo'                  => ['fg' => Color::fg256(92)], // purple
    'ok'                    => ['fg' => Color::fg256(28)], // green
    'question'              => ['fg' => Color::fg256(214)], // yellow
    'version'               => ['fg' => Color::fg256(28)], // green
    'warn'                  => ['fg' => Color::fg256(214)], // yellow

    // ========================================================================
    // BlitzPHP custom styles
    // ========================================================================

    'magenta'               => ['fg' => Color::fg256(168)], // pink
    'indigo'                => ['fg' => Color::fg256(92)],  // purple
    'purple'                => ['fg' => Color::fg256(92)],  // purple
    'orange'                => ['fg' => Color::fg256(214)], // yellow
    'pink'                  => ['fg' => Color::fg256(168)], // pink
    'brown'                 => ['fg' => Color::fg256(130)],
];
