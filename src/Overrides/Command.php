<?php

declare(strict_types=1);

namespace BlitzPHP\Console\Overrides;

use Ahc\Cli\Helper\OutputHelper;
use Ahc\Cli\Input\Command as AhcCommand;

use function Ahc\Cli\t;

/**
 * Overridden Command class with custom help display.
 *
 * @package BlitzPHP\Console\Overrides
 */
class Command extends AhcCommand
{
    /**
     * Show default help screen.
     *
     * @override
     *
     * @return mixed
     */
    public function showDefaultHelp(): mixed
    {
        $io     = $this->io();
        $helper = new OutputHelper($io->writer());
        $app    = $this->app();

        if (($logo = $this->logo()) || ($app && ($logo = $app->logo()) && $app->getDefaultCommand() === $this->_name)) {
            $io->logo($logo, true);
        }

        $usage = $this->_usage ?: $this->_name;
        $io->help_category(t('Usage') . ':', true);
        $io->help_usage("  {$usage}")->eol(2);

        $io->help_category(t('Description') . ':', true);
        $io->help_summary("  {$this->_desc}")->eol();

        $helper
            ->showArgumentsHelp($this->allArguments())
            ->showOptionsHelp($this->allOptions());

        return $this->emit('_exit', 0);
    }
}
