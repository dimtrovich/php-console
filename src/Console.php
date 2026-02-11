<?php

declare(strict_types=1);

namespace BlitzPHP\Console;

use Ahc\Cli\Application;
use BlitzPHP\Console\Exceptions\CommandNotFoundException;
use BlitzPHP\Console\Exceptions\InvalidCommandException;
use BlitzPHP\Contracts\Container\ContainerInterface;
use ReflectionClass;
use Throwable;

use function Ahc\Cli\t;

/**
 * Core console application.
 *
 * @package BlitzPHP\Console
 */
class Console extends Application
{
    /**
     * Container instance.
     */
    protected ?ContainerInterface $container = null;

	/**
	 * @var array<string, callable>
	 */
	protected array $hooks = [];

    /**
     * Flags
	 *
	 * @var array<string, bool>
     */
    protected array $flags = [
		'debug'  => false,
		'header' => true,
		'footer' => false,
	];

    /**
     * Header title.
     */
    protected string $headtitle = '';

    /**
     * Registered commands.
     *
     * @var array<string, array{
	 *      'action' => callable,}>
	 *      'name'   => string,
	 *      'alias'  => string,
	 * }>
	 *
	 * @example [
	 *     'App\Console\Commands\ExampleCommand' => [
	 *         'action' => callable,
	 *         'name'   => 'example:command',
	 *         'alias'  => 'ex:cmd'
	 *     ],
     */
    protected array $_commands = [];

	/**
	 * Command cache for quick retrieval by name or alias.
	 *
	 * @var array<string, array>
	 */
	protected array $commandCached = [];

    /**
     * Create a new console application.
     *
     * @param string $name    Application name
     * @param string $version Application version
     */
    public function __construct(string $name = '', string $version = '1.0.0')
    {
        parent::__construct($name, $version);

        $this->onException([$this, 'onError']);
	}

    /**
     * Set the container instance.
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

	/**
	 * Set a hook callback.
	 *
	 * @param string   $hook Hook name ('before' or 'after')
	 * @param callable $fn   Callback function
	 */
	public function setHook(string $hook, callable $fn): self
	{
		$this->hooks[$hook] = $fn;

		return $this;
	}

	/**
	 * Set a flag value.
	 *
	 * @param string $flag  Flag name
	 * @param bool   $value Flag value
	 */
	public function setFlag(string $flag, bool $value): self
	{
		$this->flags[$flag] = $value;

		return $this;
	}

    /**
     * Set the header title.
     */
    public function headtitle(string $headtitle): self
    {
        $this->headtitle = $headtitle;

        return $this;
    }

    /**
     * Call a registered command.
     *
     * @param string                $commandName Command name
     * @param array<string, mixed>  $arguments   Command arguments
     * @param array<string, mixed>  $options     Command options
     *
     * @return mixed Command execution result
     *
     * @throws CommandNotFoundException If command doesn't exist
     */
    public function call(string $commandName, array $arguments = [], array $options = []): mixed
    {
		$command = $this->retrieveCommand($commandName);
		$action  = $command['action'] ?? null;

        if ($action === null) {
			if (str_contains($commandName, '\\')) {
				$availables = array_keys($this->_commands);
			} else {
				$availables = array_map(fn($cmd) => $cmd['name'], $this->_commands);
			}

			$this->outputHelper()->showCommandNotFound($commandName, $availables);

            return ($this->onExit)(127);
        }

        foreach ($options as $key => $value) {
            $key = preg_replace('/^\-\-/', '', $key);
            if (!isset($options[$key])) {
                $options[$key] = $value;
            }
        }

        return $action($arguments, $options, true);
    }

    /**
     * Check if a command exists in the registered commands list.
     *
     * @param string $commandName Command name
     *
     * @return bool True if command exists, false otherwise
     */
    public function commandExists(string $commandName): bool
    {
		$command = $this->retrieveCommand($commandName);

        return !empty($command) && is_callable($command['action']);
    }

    /**
     * Add a command to the console.
     *
     * @param class-string<Command> $className Command FQCN
     *
     * @throws InvalidCommandException If command is not valid
     */
    public function addCommand(string $className): void
    {
        $class = new ReflectionClass($className);

        if (!$class->isInstantiable() || !$class->isSubclassOf(Command::class)) {
            throw new InvalidCommandException($className);
        }

        /** @var Command $instance */
        $instance = $this->container !== null ? $this->container->make($className) : $class->newInstance();

        $command = $instance->initialize($this);

        $app    = $this;
        $action = function (?array $arguments = [], ?array $options = [], ?bool $suppress = false) use ($instance, $command, $app) {
            $this->name();

            $parameters = $command->values();
            $arguments  = $arguments === [] || $arguments === null ? $command->args() : $arguments;
            $options    = $options === [] || $options === null ? array_diff_key($parameters, $arguments) : $options;
            $parameters = array_merge($options, $arguments);

            $instance->setParameters($arguments, $options);

            $app->before($suppress === true, $instance);

            $result = $app->container
                ? $app->container->call([$instance, 'handle'])
                : $instance->handle();

            $app->after($suppress === true, $instance);

            return $result;
        };

		$this->_commands[$className] = [
			'action' => $action,
			'name'   => $instance->name(),
			'alias'  => $instance->alias(),
        ];

        $this->add($command->action($action));
    }

    /**
     * Hook executed before command execution.
     *
     * @param bool    $suppress Whether output is suppressed
     * @param Command $command  Command instance
     */
    protected function before(bool $suppress, Command $command): void
    {
		if (isset($this->hooks['before'])) {
			call_user_func($this->hooks['before'], $suppress, $command);
		}
    }

    /**
     * Hook executed after command execution.
     *
     * @param bool    $suppress Whether output is suppressed
     * @param Command $command  Command instance
     */
    protected function after(bool $suppress, Command $command): void
    {
		if (isset($this->hooks['after'])) {
			call_user_func($this->hooks['after'], $suppress, $command);
		}
    }

    /**
     * Error handler for exceptions.
     *
     * @param Throwable $e        Exception
     * @param int       $exitCode Exit code
     */
    protected function onError(Throwable $e, int $exitCode): void
    {
        $this->io()->error($e->getMessage(), true);

        if ($this->flags['debug']) {
            $this->io()->error($e->getTraceAsString(), true);
        }

        exit($exitCode);
    }

    /**
     * Show default help screen.
     *
     * @override
     */
    public function showDefaultHelp(): mixed
    {
        $writer = $this->io()->writer();

        $header = !$this->flags['header'] ? '' : (
            $this->headtitle ? "\n{$this->headtitle}" : "\n{$this->name}, " . t('version') . " {$this->version}"
        );

        $footer = !$this->flags['footer'] ? '' : t('Run `<command> --help` for specific help');

        if ($this->logo) {
            $writer->logo($this->logo, true);
        }

        $this->outputHelper()->showCommandsHelp($this->commands(), $header, $footer);

        return ($this->onExit)();
    }

	/**
	 * Retrieve a registered command by name or alias.
	 *
	 * @param string $name Command name or alias
	 *
	 * @return array{action: callable, name: string, alias: string}|null Command data if found, null otherwise
	 */
	private function retrieveCommand(string $name): ?array
	{
		if ($name === '') {
			return null;
		}

		if (array_key_exists($name, $this->commandCached)) {
			return $this->commandCached[$name];
		}

		foreach ($this->_commands as $classname => $command) {
			if (in_array($name, [$classname, $command['name'], $command['alias']], true)) {
				return $this->commandCached[$name] = $command;
			}
		}

		return $this->commandCached[$name] = null;
	}
}
