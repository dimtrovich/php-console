<?php

namespace BlitzPHP\Console\Traits;

use Ahc\Cli\Helper\Terminal;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use BlitzPHP\Console\Overrides\Cursor;
use BlitzPHP\Console\Overrides\ProgressBar;

/**
 * @property Color $color
 * @property Cursor $cursor
 * @property Terminal $terminal
 * @property Writer $writer
 */
trait InteractsWithOutput
{
	/**
     * Afficher un compteur
     */
    public function counter(int $start = 0, int $end = 100, int $step = 1): void
    {
        for ($i = $start; $i <= $end; $i += $step) {
            $this->write($this->cursor->col(-4))->write(sprintf('%3d%%', $i));
            usleep(50000);
        }
        $this->eol();
    }

	/**
     * Écrit un message et le formate selon le type
     * Compatible avec Laravel: line(), info(), comment(), question(), error()
     */
    public function line(string $message, ?string $color = null, int $verbosity = 1): self
    {
        if ($color) {
            return $this->colorize($message, $color);
        }

        return $this->write($message)->eol();
    }

	/**
     * Affiche un message de question
     */
    public function question(string $message): self
    {
		$this->writer->question($message);

		return $this;
    }

	/**
     * Affiche un message d'alerte
     * Compatible avec Laravel: alert()
     */
    public function alert(string $message, string $color = 'yellow'): self
    {
        $this->newLine();
        $this->colorize(str_repeat('*', strlen($message) + 12), $color);
        $this->colorize('*     ' . $message . '     *', $color);
        $this->colorize(str_repeat('*', strlen($message) + 12), $color);
        $this->newLine();

        return $this;
    }

	/**
     * Affiche une liste à puces
     */
    public function bulletList(array $items, string $title = '', string $color = 'yellow'): self
    {
        if ($title) {
            $this->colorize($title, $color);
        }

        foreach ($items as $item) {
            $this->write("  • {$item}")->eol();
        }

        return $this;
    }

    /**
     * Affiche une liste numérotée
     */
    public function numberedList(array $items, string $title = '', string $color = 'yellow'): self
    {
        if ($title) {
            $this->colorize($title, $color);
        }

        foreach ($items as $index => $item) {
			$this->writer->colors(sprintf("  <green>%d.</end> %s", $index + 1, $item))->eol();
        }

        return $this;
    }

	/**
     * Ecrit un message dans une couleur spécifique
     */
    public function colorize(string $message, string $color): self
    {
        $this->writer->colors('<' . $color . '>' . $message . '</end><eol>');

        return $this;
    }

    /**
     * Ecrit un message de reussite
     */
    public function ok(string $message, bool $eol = false): self
    {
        $this->writer->ok($message, $eol);

        return $this;
    }

    /**
     * Ecrit un message d'echec
     */
    public function fail(string $message, bool $eol = false): self
    {
        $this->writer->error($message, $eol);

        return $this;
    }

    /**
     * Ecrit un message de succes
     */
    public function success(string $message, bool $badge = true, string $label = 'SUCCESS'): self
    {
        if (! $badge) {
            $this->writer->okBold($label);
        } else {
            $this->writer->boldWhiteBgGreen(" {$label} ");
        }

        return $this->write(' ' . $message, true);
    }

    /**
     * Ecrit un message d'avertissement
     */
    public function warning(string $message, bool $badge = true, string $label = 'WARNING'): self
    {
        if (! $badge) {
            $this->writer->warnBold($label);
        } else {
            $this->writer->boldWhiteBgYellow(" {$label} ");
        }

        return $this->write(' ' . $message, true);
    }

    /**
     * Ecrit un message d'information
     */
    public function info(string $message, bool $badge = true, string $label = 'INFO'): self
    {
        if (! $badge) {
            $this->writer->infoBold($label);
        } else {
            $this->writer->boldWhiteBgCyan(" {$label} ");
        }

        return $this->write(' ' . $message, true);
    }

    /**
     * Ecrit un message d'erreur
     */
    public function error(string $message, bool $badge = true, string $label = 'ERROR'): self
    {
        if (! $badge) {
            $this->writer->errorBold($label);
        } else {
            $this->writer->boldWhiteBgRed(" {$label} ");
        }

        return $this->write(' ' . $message, true);
    }

    /**
     * Ecrit la tâche actuellement en cours d'execution
     */
    public function task(string $task, ?int $sleep = null): self
    {
        $this->write('>> ' . $task, true);

        if ($sleep !== null) {
            sleep($sleep);
        }

        return $this;
    }

    /**
     * Écrit EOL n fois.
     */
    public function eol(int $n = 1): static
    {
        $this->writer->eol($n);

        return $this;
    }

    /**
     * Écrit une nouvelle ligne vide (saut de ligne).
     */
    public function newLine(): self
    {
        return $this->eol(1);
    }

    /**
     * Générer une table pour la console. Les clés de la première ligne sont prises comme en-tête.
     *
     * @param list<array> $rows   Tableau de tableaux associés.
     * @param array       $styles Par exemple : ['head' => 'bold', 'odd' => 'comment', 'even' => 'green']
     */
    public function table(array $rows, array $styles = []): self
    {
        $this->writer->table($rows, $styles);

        return $this;
    }

    /**
     * Écrit le texte formaté dans stdout ou stderr.
     */
    public function write(string $texte, bool $eol = false): self
    {
        $this->writer->write($texte, $eol);

        return $this;
    }

    /**
     * Écrit le texte de maniere commentée.
     */
    public function comment(string $text, bool $eol = false): self
    {
        $this->writer->comment($text, $eol);

        return $this;
    }

    /**
     * Efface la console
     */
    public function clear(): self
    {
        $this->cursor->clear();

        return $this;
    }

    /**
     * Affiche une bordure en pointillés
     */
    public function border(?int $length = null, string $char = '-'): self
    {
        $length = $length ?: ($this->terminal->width() ?: 100);
        $str    = str_repeat($char, $length);
        $str    = substr($str, 0, $length);

        return $this->comment($str, true);
    }

    /**
     * Affiche les donnees formatees en json
     *
     * @param mixed $data
     */
    public function json($data): self
    {
        $this->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), true);

        return $this;
    }

    /**
     * Effectue des tabulations
     */
    public function tab(int $repeat = 1): self
    {
        $this->write(str_repeat("\t", $repeat));

        return $this;
    }

	/**
     * Initialise une bar de progression
     */
    public function progress(?int $total = null): ProgressBar
    {
        return new ProgressBar($total, $this->writer);
    }

    /**
     * Ecrit deux textes de maniere justifiee dans la console (l'un a droite, l'autre a gauche)
     */
    public function justify(string $first, ?string $second = '', array $options = []): self
    {
		$this->writer->justify($first, $second, $options);

        return $this;
    }

    /**
     * Ecrit un texte au centre de la console
     */
    public function center(string $text, array $options = []): self
    {
        $sep = $options['sep'] ?? ' ';
        unset($options['sep']);

        $dashWidth = ($this->terminal->width() ?: 100) - strlen($text);
        $dashWidth -= 2;
        $dashWidth = (int) ($dashWidth / 2);

        $text     = $this->color->line($text, $options);
        $repeater = str_repeat($sep, $dashWidth);

        return $this->write($repeater . ' ' . $text . ' ' . $repeater)->eol();
    }

	/**
     * Clears the screen of output
     *
     * @return void
     */
    public function clearScreen()
    {
        // Unix systems, and Windows with VT100 Terminal support (i.e. Win10) can handle CSI sequences.
		// For lower than Win10 we just shove in 40 new lines.
		if ($this->terminal->isWindows() && (function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT))) {
			$this->eol(40);
		} else {
			$this->writer->raw("\033[H\033[2J");
		}
    }
}
