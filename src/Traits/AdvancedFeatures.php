<?php

namespace BlitzPHP\Console\Traits;

use Throwable;

use function Ahc\Cli\t;

trait AdvancedFeatures
{
	use InteractsWithInput, InteractsWithOutput;

    /**
     * Waits a certain number of seconds, optionally showing a wait message and
     * waiting for a key press.
     *
     * @param int  $seconds   Number of seconds
     * @param bool $countdown Show a countdown or not
     *
     * @return void
     */
    public function wait(int $seconds, bool $countdown = false, string $wait_msg = 'Press any key to continue...')
    {
        if ($countdown) {
            $time = $seconds;

            while ($time > 0) {
				$this->writer->raw($time . '... ');
                sleep(1);
                $time--;
            }

			$this->writer->raw('' . PHP_EOL);
        } elseif ($seconds > 0) {
            sleep($seconds);
        } else {
			$this->writer->raw($wait_msg . ' ');
			$this->reader->read();
        }
    }

	public function pause(string $message = 'Press any key to continue...'): void
	{
		$this->wait(0, false, $message);
	}

	/**
     * Affiche un spinner pendant l'exécution
     * Compatible avec Laravel: withSpinner()
     */
    public function withSpinner(callable $callback, string $message = 'Processing...'): mixed
    {
        $spinner = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
        $this->write($message . ' ');

        $result = null;
        $finished = false;

        // Démarrer un timer pour le spinner
        $startTime = microtime(true);
        $i = 0;

        // Exécuter dans un processus séparé ou avec pcntl_fork si disponible
        if (function_exists('pcntl_fork')) {
            $pid = pcntl_fork();
            if ($pid == 0) {
                // Processus enfant
                 try {
					$result = $callback();
					exit(0);
				} catch (Throwable) {
					exit(1);
				}
            } else {
                // Processus parent - afficher le spinner
                while (pcntl_waitpid($pid, $status, WNOHANG) == 0) {
					$this->write($this->cursor->left(1));
					$this->write($spinner[$i % count($spinner)]);
                    usleep(100000);
                    $i++;
                }
                $this->write($this->cursor->left(1))->write('✓')->eol();
            }
        } else {
            // Fallback sans fork
            $this->write($this->cursor->hide());
            $result = $callback();
            $this->write($this->cursor->show());
			$this->write($this->cursor->left(1))->write('✓')->eol();
        }

        return $result;
    }

	/**
     * Affiche un ASCII Art
     */
    public function asciiArt(string $text, string $font = 'standard'): self
    {
        $fonts = [
            'standard' => [
                'A' => '  ██  ', 'B' => '████ ', 'C' => ' ████', 'D' => '████ ', 'E' => '█████',
                // ... définir tous les caractères
            ],
        ];

        if (isset($fonts[$font])) {
            $lines = [];
            foreach (str_split(strtoupper($text)) as $char) {
                if (isset($fonts[$font][$char])) {
                    $lines[] = $fonts[$font][$char];
                }
            }

            foreach ($lines as $line) {
                $this->write($line, true);
            }
        }

        return $this;
    }

    /**
     * Affiche une timeline
     */
    public function timeline(array $events): self
    {
        $this->colorize('Timeline:', 'yellow');

        foreach ($events as $index => $event) {
            $status = $event['status'] ?? 'pending';
            $icon = match($status) {
                'completed' => '✓',
                'failed' => '✗',
                'processing' => '↻',
                default => '○'
            };

            $color = match($status) {
                'completed' => 'green',
                'failed' => 'red',
                'processing' => 'yellow',
                default => 'gray'
            };

            $this->writer->colors(sprintf(
                "  <%s>%s</end> %s",
                $color,
                $icon,
                $event['description'] ?? 'Event ' . ($index + 1)
            ))->eol();
        }

        return $this;
    }

    /**
     * Affiche une carte thermique (heatmap) en ASCII
     */
    public function heatmap(array $data, array $colors = ['░', '▒', '▓', '█']): self
    {
        $max = max($data);
        $min = min($data);
        $range = $max - $min;

        foreach ($data as $value) {
            $percentage = $range > 0 ? ($value - $min) / $range : 0.5;
            $index = (int)($percentage * (count($colors) - 1));
            $this->write($colors[$index]);
        }

        return $this->newLine();
    }

	/**
     * Exécute une tâche avec une barre de progression
     * Compatible avec Laravel: withProgressBar()
     */
    public function withProgressBar(iterable|int $items, callable $callback): void
    {
		$bar = $this->progress(is_iterable($items) ? count($items) : $items);

        if (is_iterable($items)) {
            foreach ($items as $key => $value) {
                $callback($value, $bar, $key);

                $bar->advance();
            }
        } else {
            $callback($bar);
        }

        $bar->finish();

        $this->newLine();
    }

	/**
     * Crée un menu interactif
     */
    public function menu(string $title, array $options, ?string $default = null): mixed
    {
        $this->colorize($title, 'yellow');

        foreach ($options as $key => $option) {
            $this->writer->colors(sprintf("  <green>%s</end> %s", $key, $option['label'] ?? $option))->eol();
        }

        $choice = $this->ask(t('Choose an option: '), $default);

        return $options[$choice] ?? $choice;
    }

	/**
     * Affiche une carte (grid) en ASCII
     */
    public function grid(array $data, ?callable $formatter = null): self
    {
        $formatter = $formatter ?? fn($cell) => $cell;

        // Trouver la largeur maximale de chaque colonne
        $colWidths = [];
        foreach ($data as $row) {
            foreach ($row as $colIndex => $cell) {
                $width = strlen((string)$formatter($cell));
                $colWidths[$colIndex] = max($colWidths[$colIndex] ?? 0, $width);
            }
        }

        // Afficher la grille
        foreach ($data as $row) {
            $line = '';
            foreach ($row as $colIndex => $cell) {
                $formatted = $formatter($cell);
                $line .= str_pad($formatted, $colWidths[$colIndex] + 2);
            }
            $this->write($line)->eol();
        }

        return $this;
    }

    /**
     * Affiche un graphique en ASCII
     */
    public function chart(array $data, string $type = 'bar', int $height = 10): self
    {
        $max = max($data);

        if ($type === 'bar') {
            foreach ($data as $label => $value) {
                $barLength = (int)(($value / $max) * $height * 2);
                $bar = str_repeat('█', $barLength);
                $this->write(sprintf("%-20s %s %d", $label, $bar, $value))->eol();
            }
        } elseif ($type === 'pie') {
            // Implémentation simplifiée d'un camembert ASCII
            $total = array_sum($data);
            $this->colorize(t('Pie Chart'), 'yellow');

            foreach ($data as $label => $value) {
                $percentage = ($value / $total) * 100;
                $this->write(sprintf("  %s: %.1f%%", $label, $percentage))->eol();
            }
        }

        return $this;
    }

    /**
     * Affiche un compteur en temps réel
     */
    public function liveCounter(callable $updater, int $step = 10, string $label = 'Counter', int $interval = 1000000): void
    {
        $this->write($this->cursor->hide());

        for ($i = 0; $i < $step; $i++) {
            $value = $updater($i);
            $this->write($this->cursor->left(20));
            $this->write("{$label}: " . str_pad($value, 10));
            usleep($interval);
        }

        $this->write($this->cursor->show())->eol();
    }

    /**
     * Affiche une animation
     */
    public function animation(array $frames, int $iterations = 3, int $delay = 100000): self
    {
        $this->write($this->cursor->hide());

        for ($i = 0; $i < $iterations; $i++) {
            foreach ($frames as $frame) {
                $this->write($this->cursor->eraseLine());
                $this->write($frame);
                usleep($delay);
            }
        }

        $this->write($this->cursor->show());
        return $this;
    }

    /**
     * Joue un son (si supporté)
     */
    public function beep(int $count = 1): self
    {
		// echo str_repeat("\x07", $count);

        for ($i = 0; $i < $count; $i++) {
            $this->write("\x07");
            usleep(200000);
        }

        return $this;
    }


    /**
     * Affiche une notification système (OS dépendant)
     */
    public function notify(string $title, string $message): self
    {
        $os = PHP_OS_FAMILY;

        if ($os === 'Darwin') { // macOS
            exec("osascript -e 'display notification \"{$message}\" with title \"{$title}\"'");
        } elseif ($os === 'Linux') {
            exec("notify-send \"{$title}\" \"{$message}\"");
        } elseif ($os === 'Windows') {
            // PowerShell pour Windows
            exec("powershell -Command \"[System.Media.SystemSounds]::Beep.Play()\"");
        }

        return $this;
    }
}
