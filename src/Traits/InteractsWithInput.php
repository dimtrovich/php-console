<?php

namespace BlitzPHP\Console\Traits;

use Ahc\Cli\Input\Reader;
use Ahc\Cli\IO\Interactor;
use InvalidArgumentException;

use function Ahc\Cli\t;

/**
 * @property Interactor $io
 * @property Reader $reader
 */
trait InteractsWithInput
{
	/**
     * Demander à l'utilisateur d'entrer une donnée
     */
    public function ask(string $question, mixed $default = null): mixed
    {
        return $this->prompt($question, $default);
    }

	/**
     * Demander à l'utilisateur une entrée secrète (alias de promptHidden)
     */
    public function secret(string $text, ?callable $fn = null, int $retry = 3): mixed
    {
        return $this->promptHidden($text, $fn, $retry);
    }

	/**
     * Demander avec validation et complétion
     */
    public function askWithCompletion(string $question, array $choices, mixed $default = null): mixed
    {
        return $this->prompt($question, $default, function ($input) use ($choices) {
            if (!in_array($input, $choices)) {
                throw new InvalidArgumentException("La valeur doit être parmi: " . implode(', ', $choices));
            }
            return $input;
        });
    }

	/**
     * Laissez l'utilisateur faire un choix parmi les choix disponibles.
     *
     * @param string $question    Texte d'invite.
     * @param array  $choices Choix possibles pour l'utilisateur.
     * @param mixed  $default Valeur par défaut - si non choisie ou invalide.
     * @param bool   $case    Si l'entrée utilisateur doit être sensible à la casse.
     *
     * @return mixed Entrée utilisateur ou valeur par défaut.
     */
    public function choice(string $question, array $choices, $default = null, bool $case = false): mixed
    {
		$this->writer->question($question)->eol();

		foreach ($choices as $key => $value) {
			$this->writer->choice(str_pad("  [$key]", 6))->answer($value)->eol();
		}

		$choice = $this->prompt(t('Choice'));

		return $this->validChoice($choice, $choices, $default, $case);
    }

	/**
     *
     */
    protected function validChoice($choice, array $choices, mixed $default, bool $case): mixed
    {
		if (array_key_exists($choice, $choices)) {
			return $choices[$choice];
		}

		$fn = ['\strcasecmp', '\strcmp'][(int) $case];

        foreach ($choices as $option) {
            if ($fn($choice, $option) == 0) {
                return $option;
            }
        }

		return $choices[$default] ?? $default;
    }

    /**
     * Laissez l'utilisateur faire plusieurs choix parmi les choix disponibles.
     *
     * @param string $question    Texte d'invite.
     * @param array  $choices Choix possibles pour l'utilisateur.
     * @param mixed  $default Valeur par défaut - si non choisie ou invalide.
     * @param bool   $case    Si l'entrée utilisateur doit être sensible à la casse.
     *
     * @return array Entrée utilisateur ou valeur par défaut.
     */
    public function choices(string $question, array $choices, $default = null, bool $case = false): array
    {
		$this->writer->question($question)->eol();

		foreach ($choices as $key => $value) {
			$this->writer->choice(str_pad("  [$key]", 6))->answer($value)->eol();
		}

		$choice = $this->prompt(t('Choices (comma separated)'));

		if (is_string($choice)) {
            $choice = explode(',', str_replace(' ', '', $choice));
        }

        $valid = array_map(fn($option) => $this->validChoice($option, $choices, $default, $case), $choice);

        return array_values(array_unique(array_filter($valid)));
    }

    /**
     * Confirme si l'utilisateur accepte une question posée par le texte donné.
     *
     * @param string $default `y|n`
     */
    public function confirm(string $text, string $default = 'y'): bool
    {
        return $this->io->confirm($text, $default);
    }

    /**
     * Demander à l'utilisateur d'entrer une donnée
     *
     * @param callable|null $fn      L'assainisseur/validateur pour l'entrée utilisateur
     *                               Tout message d'exception est imprimé et démandé à nouveau.
     * @param int           $retry   Combien de fois encore pour réessayer en cas d'échec.
     * @param mixed|null    $default
     */
    public function prompt(string $text, $default = null, ?callable $fn = null, int $retry = 3): mixed
    {
        return $this->io->prompt($text, $default, $fn, $retry);
    }

    /**
     * Demander à l'utilisateur une entrée secrète comme un mot de passe. Actuellement pour unix uniquement.
     *
     * @param callable|null $fn    L'assainisseur/validateur pour l'entrée utilisateur
     *                             Tout message d'exception est imprimé en tant qu'erreur.
     * @param int           $retry Combien de fois encore pour réessayer en cas d'échec.
     */
    public function promptHidden(string $text, ?callable $fn = null, int $retry = 3): mixed
    {
        return $this->io->promptHidden($text, $fn, $retry);
    }
}
