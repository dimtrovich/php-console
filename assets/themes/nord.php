<?php

use Ahc\Cli\Output\Color;

/**
 * Nord theme - arctic, north-bluish color palette.
 * Clean and calm, perfect for professional environments.
 */
return [
    // ========================================================================
    // Built-in adhocore/cli styles mapped to Nord
    // ========================================================================

    'help_header'           => ['fg' => Color::fg256(109)], // frost-2
    'help_item_even'        => ['fg' => Color::fg256(109)], // frost-2
    'help_item_odd'         => ['fg' => Color::fg256(75)],  // frost-3
    'help_group'            => ['fg' => Color::fg256(150), 'bold' => 1], // aurora-4
    'help_category'         => ['fg' => Color::fg256(179)], // aurora-3
    'help_usage'            => ['fg' => Color::fg256(251)], // snow-storm-1
    'help_description_even' => ['fg' => Color::fg256(251)], // snow-storm-1
    'help_description_odd'  => ['fg' => Color::fg256(252)], // snow-storm-2
    'help_summary'          => ['fg' => Color::fg256(251)], // snow-storm-1
    'help_example'          => ['fg' => Color::fg256(240)], // polar-night-4
    'help_text'             => ['fg' => Color::fg256(251)], // snow-storm-1
    'help_footer'           => ['fg' => Color::fg256(240)], // polar-night-4

    'answer'                => ['fg' => Color::fg256(109), 'bold' => 1], // frost-2
    'choice'                => ['fg' => Color::fg256(150), 'bold' => 1], // aurora-4
    'comment'               => ['fg' => Color::fg256(179)], // aurora-3
    'error'                 => ['fg' => Color::fg256(167), 'bold' => 1], // aurora-1
    'info'                  => ['fg' => Color::fg256(75)], // frost-3
    'logo'                  => ['fg' => Color::fg256(140)], // aurora-5
    'ok'                    => ['fg' => Color::fg256(150)], // aurora-4
    'question'              => ['fg' => Color::fg256(179)], // aurora-3
    'version'               => ['fg' => Color::fg256(150)], // aurora-4
    'warn'                  => ['fg' => Color::fg256(172)], // aurora-2

    // ========================================================================
    // BlitzPHP custom styles
    // ========================================================================

    'magenta'               => ['fg' => Color::fg256(140)], // aurora-5
    'indigo'                => ['fg' => Color::fg256(75)], // frost-3
    'purple'                => ['fg' => Color::fg256(140)], // aurora-5
    'orange'                => ['fg' => Color::fg256(172)], // aurora-2
    'pink'                  => ['fg' => Color::fg256(168)],
    'brown'                 => ['fg' => Color::fg256(130)],
];
