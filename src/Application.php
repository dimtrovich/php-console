<?php

namespace BlitzPHP\Console;

use Ahc\Cli\Application as BaseApplication;
use Ahc\Cli\Output\Color;
use BlitzPHP\Console\Exceptions\CommandNotFoundException;
use BlitzPHP\Console\Exceptions\InvalidCommandException;
use BlitzPHP\Contracts\Container\ContainerInterface;
use ReflectionClass;
use Throwable;

use function Ahc\Cli\t;

class Application extends BaseApplication
{
	protected ?ContainerInterface $container = null;

    protected bool $debug = false;

	protected string $headtitle = '';

	protected bool $showHeader = true;
	protected bool $showFooter = true;


    /**
     * Liste des commandes
     *
     * @var array<string, callable>
     */
    protected array $_commands = [];


	public function __construct(string $name = '', string $version = '1.0.0', string $locale = 'en')
	{
		parent::__construct($name, $version);

		$this->onException([$this, 'onError']);

		if ($locale !== 'en' && file_exists($path = __DIR__ . "/locales/{$locale}.php")) {
			$this->addLocale($locale, require $path, true);
		}

		$this->defineColors(['help_header', 'help_item_even', 'help_item_odd'], Color::GREEN);
		$this->defineColors(['help_group'], Color::fg256(49));
		$this->defineColors(['help_category'], Color::YELLOW);
		$this->defineColors(['help_usage', 'help_description_even', 'help_description_odd', 'help_summary'], Color::WHITE);
	}

	protected function defineColors(array $names, int|string $fg): self
	{
		foreach ($names as $name) {
			Color::style($name, ['fg' => $fg]);
		}

		return $this;
	}

	public function container(ContainerInterface $container): self
	{
		$this->container = $container;

		return $this;
	}

	public function headtitle(string $headtitle): self
	{
		$this->headtitle = $headtitle;

		return $this;
	}

	public function showHeader(bool $showHeader): self
	{
		$this->showHeader = $showHeader;

		return $this;
	}

	public function showFooter(bool $showFooter): self
	{
		$this->showFooter = $showFooter;

		return $this;
	}

    /**
     * Appelle une commande deja enregistree
     * Utile pour executer une commande dans une autre commande ou dans un controleur
	 *
	 * @throws CommandNotFoundException
     */
    public function call(string $commandName, array $arguments = [], array $options = [])
    {
        $action = $this->_commands[$commandName] ?? null;

        if ($action === null) {
			throw new CommandNotFoundException($commandName);
        }

        foreach ($options as $key => $value) {
            $key = preg_replace('/^\-\-/', '', $key);
            if (! isset($options[$key])) {
                $options[$key] = $value;
            }
        }

        return $action($arguments, $options, true);
    }

    /**
     * Verifie si une commande existe dans la liste des commandes enregistrees
     */
    public function commandExists(string $commandName): bool
    {
        return ! empty($this->_commands[$commandName]) && is_callable($this->_commands[$commandName]);
    }

	/**
	 * @inheritDoc
	 * @override
	 */
    public function handle(array $argv): mixed
    {
		$this->debug    = in_array('--debug', $argv, true);
		Color::$enabled = ! in_array('--no-colors', $argv, true);

        $argv = array_filter($argv, fn($arg) => !in_array($arg, ['--debug', '--no-colors']));

		return parent::handle($argv);
    }

    /**
	 * @inheritDoc
	 * @override
	 */
    public function showDefaultHelp(): mixed
    {
		$writer = $this->io()->writer();

		$header = !$this->showHeader ? '' : (
			$this->headtitle ?  "\n{$this->headtitle}" : "\n{$this->name}, " . t('version') . " {$this->version}"
		);
		$footer = !$this->showFooter ? '' : t('Run `<command> --help` for specific help');

		if ($this->logo) {
            $writer->logo($this->logo, true);
        }

        $this->outputHelper()->showCommandsHelp($this->commands(), $header, $footer);

        return ($this->onExit)();
    }

	 /**
	 * Ajoute plusieurs commandes à la console
	 *
	 * @param list<class-string<Command>> $commands Tableau de FQCN de commandes
	 */
	public function addCommands(array $commands): void
	{
		foreach ($commands as $command) {
			$this->addCommand($command);
		}
	}

    /**
     * Ajoute une commande à la console
     *
     * @param class-string<Command> $className FQCN de la commande
     */
    public function addCommand(string $className)
    {
        $class  = new ReflectionClass($className);

        if (! $class->isInstantiable() || ! $class->isSubclassOf(Command::class)) {
			throw new InvalidCommandException($className);
        }

        /** @var Command $instance */
		$instance = $this->container !== null ? $this->container->make($className) : $class->newInstance();

		$command = $instance->initialize($this);

		$app    = $this;
		$action = function (?array $arguments = [], ?array $options = [], ?bool $suppress = false) use ($instance, $command, $app) {
            $this->name(); // ne pas retirer. car en cas, d'absence, cs-fixer mettra cette fonction en static. Et php-cli generera une erreur

			$parameters = $command->values();
			$arguments  = $arguments === [] || $arguments === null ? $command->args() : $arguments;
			$options    = $options   === [] || $options === null ? array_diff_key($parameters, $arguments) : $options;
            $parameters = array_merge($options, $arguments);

			$instance->setParameters($arguments, $options);

			$app->before($suppress === true, $instance);

            $result = $app->container
				? $app->container->call([$instance, 'handle'])
				: $instance->handle();

			$app->after($suppress === true, $instance);

			return $result;
        };

        $command->action($action);

        $this->_commands[$instance->name] = $action;

        $this->add($command, $instance->alias, false);
    }

	public function before(bool $suppress, Command $command): void
	{
		// Hook avant l'exécution de la commande
	}

	public function after(bool $suppress, Command $command): void
	{
		// Hook après l'exécution de la commande
	}

    protected function onError(Throwable $e, int $exitCode): void
	{
		$this->io()->error($e->getMessage(), true);

        if ($this->debug) {
            $this->io()->error($e->getTraceAsString(), true);
        }

		exit($exitCode);
    }
}
