<?php

namespace BlitzPHP\Console\Overrides;

use Ahc\Cli\Output\ProgressBar as AhcProgressBar;
use Ahc\Cli\Output\Writer;

use function Ahc\Cli\t;

class ProgressBar extends AhcProgressBar
{
	protected array $messages = [];
    protected float $startTime;
    protected int $current = 0;

    public function __construct(?int $total = null, ?Writer $writer = null)
    {
        parent::__construct($total, $writer);

        $this->startTime = microtime(true);
    }

    /**
     * Avance avec un message personnalisé
     */
    public function advanceWithMessage(int $step = 1, string $message = ''): void
    {
        $this->current += $step;

        if ($message) {
            $this->messages[] = $message;
        }

        $this->advance($step);
    }

    /**
     * Affiche des statistiques
     */
    public function showStats(): void
    {
        $elapsed = microtime(true) - $this->startTime;
        $speed = $this->current > 0 ? $this->current / $elapsed : 0;

        $this->writer->colors(sprintf(
            "\n<yellow>%s:</end> %d items en %.2fs (%.2f items/s)",
			t('Statistics'),
            $this->current,
            $elapsed,
            $speed
        ))->eol();

        if (!empty($this->messages)) {
            $this->writer->colors('<yellow>' . t('Messages') . ':</end>');
            foreach ($this->messages as $message) {
                $this->writer->write("  • " . $message)->eol();
            }
        }
    }

    /**
     * Affiche une barre de progression avec pourcentage
     */
    public function display(): void
    {
        if ($this->total) {
            $percent = (int)(($this->current / $this->total) * 100);
            $barLength = 50;
            $filled = (int)($barLength * $percent / 100);

            $bar = str_repeat('█', $filled) . str_repeat('░', $barLength - $filled);
            $this->writer->write(sprintf("\r[%s] %3d%%", $bar, $percent));
        }
    }
}
