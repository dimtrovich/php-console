<?php

namespace BlitzPHP\Console;

use Ahc\Cli\Helper\Terminal;
use Ahc\Cli\Input\Reader;
use Ahc\Cli\IO\Interactor;
use Ahc\Cli\Output\Color;
use Ahc\Cli\Output\Writer;
use BlitzPHP\Console\Exceptions\CommandNotFoundException;
use BlitzPHP\Console\Overrides\Command as BaseCommand;
use BlitzPHP\Console\Overrides\Cursor;
use BlitzPHP\Console\Traits\AdvancedFeatures;
use BlitzPHP\Console\Traits\InteractsWithParameters;
use InvalidArgumentException;

/**
 * Classe de base utilisée pour créer des commandes pour la console
 *
 * @param string $alias Alias de la commande
 * @param string $name Nom de la commande
 */
abstract class Command
{
	use AdvancedFeatures, InteractsWithParameters;

	/**
     * Le groupe sous lequel la commande est regroupée dans la liste des commandes.
     */
    protected string $group = '';

    /**
     * Le nom de la commande
     */
    protected string $name;

    /**
     * La description courte de la commande
     */
    protected string $description = '';

    /**
     * Utilisation de la commande
     */
    protected string $usage = '';

    /**
     * Options de la commande
     *
     * @var array<string, mixed>
     *
     * @example
     * `[
     *      'option' => [string $description, mixed|null $default_value, callable|null $filter]
     * ]`
     */
    protected array $options = [];

    /**
     * La description des arguments de la commande
     *
     * @var array<string, mixed>
     *
     * @example
     * `[
     *      'argument' => [string $description, mixed|null $default_value]
     * ]`
     */
    protected array $arguments = [];

    /**
     * L'alias de la commande
     */
    protected string $alias = '';

    /**
     * La version de la commande
     */
    protected string $version = '';


	protected Application $app;
	protected Interactor $io;
    protected Writer $writer;
    protected Reader $reader;
    protected Color $color;
    protected Cursor $cursor;
    protected Terminal $terminal;

    /**
     * Exécution réelle de la commande.
	 *
	 * Cette méthode doit être implémentée par les classes qui étendent Command pour définir le comportement de la commande lorsqu'elle est exécutée.
	 *
	 * @param array<string, mixed> $params Les paramètres reçus par la commande, comprenant les arguments et les options.
     */
    abstract public function handle();

	/**
	 * Initalisation des proprieté necessaires
	 *
	 * @internal
	 */
	public function initialize(Application $app): BaseCommand
    {
		$this->app      = $app;
		$this->io       = $this->app->io();
		$this->writer   = $this->io->writer();
		$this->reader   = $this->io->reader();
		$this->color    = $this->writer->colorizer();
		$this->cursor   = new Cursor();
		$this->terminal = $this->writer->terminal();

		return $this->createCommand();
    }

    /**
     * Peut etre utiliser par la commande pour executer d'autres commandes.
	 *
	 * @throws CommandNotFoundException
     */
    public function call(string $command, array $arguments = [], array $options = []): mixed
    {
        return $this->app->call($command, $arguments, $options);
    }

    /**
     * Peut etre utiliser par la commande pour verifier si une commande existe dans la liste des commandes enregistrees
     */
    public function commandExists(string $commandName): bool
    {
        return $this->app->commandExists($commandName);
    }

	/**
     * La chaîne de caractères est remplacée par des titres de la même longueur pour que les descriptions soient bien alignées.
     *
     * @param int $extra Nombre d'espaces supplémentaires à ajouter à la fin
     */
    public function pad(string $item, int $max, int $extra = 2, int $indent = 0): string
    {
        $max += $extra + $indent;

        return str_pad(str_repeat(' ', $indent) . $item, $max);
    }


    /**
     * Facilite l'accès à nos propriétés protégées.
     */
    public function __get(string $key)
    {
		if ( in_array($key, ['name', 'alias'])) {
			return $this->{$key};
		}

		throw new InvalidArgumentException(sprintf('Propriete invalide: %s', $key));
    }

	private function createCommand(): BaseCommand
	{
		$command = new BaseCommand(
            $this->name,
            $this->description,
            false,
            $this->app
        );

		$this->configure($command);

		return $command;
	}

	/**
	 * Configure la commande en définissant ses options, arguments, usage, version, etc.
	 * Cette méthode peut être surchargée par les classes qui étendent Command pour personnaliser la configuration de la commande.
	 */
	protected function configure(BaseCommand $command)
	{
		$command->inGroup($this->group) // Defini le groupe auquel appartient la commande
				->usage($this->usage) // Defini l'usage de la commande
				->version($this->version); // Defini la version de la commande

		$this->defineOptions($command);
		$this->defineArguments($command);
	}

	/**
	 * Defini les options de la commande
	 */
	protected function defineOptions(BaseCommand $command): void
	{
		foreach ($this->options as $option => $value) {
            $value = (array) $value;

            $description = $value[0];
            if (! is_string($description)) {
                continue;
            }

            $default = $value[1] ?? null;
            $filter  = $value[2] ?? null;
            if ($filter !== null && ! is_callable($filter)) {
                $filter = null;
            }

            $command->option($option, $description, $filter, $default);
        }
	}

	/**
	 * Defini les arguments de la commande
	 */
	protected function defineArguments(BaseCommand $command): void
	{
		foreach ($this->arguments as $argument => $value) {
            $value = (array) $value;

            $description = $value[0];
            if (! is_string($description)) {
                continue;
            }

            $default = $value[1] ?? null;

            $command->argument($argument, $description, $default);
        }
	}
}
