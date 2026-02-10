<?php

declare(strict_types=1);

namespace BlitzPHP\Console;

use Ahc\Cli\Application as BaseApplication;
use Ahc\Cli\Output\Color;
use BlitzPHP\Console\Exceptions\CommandNotFoundException;
use BlitzPHP\Console\Exceptions\InvalidCommandException;
use BlitzPHP\Contracts\Container\ContainerInterface;
use ReflectionClass;
use Throwable;

use function Ahc\Cli\t;

/**
 * Console application.
 *
 * @package BlitzPHP\Console
 */
class Application extends BaseApplication
{
    /**
     * Container instance.
     */
    protected ?ContainerInterface $container = null;

    /**
     * Debug mode flag.
     */
    protected bool $debug = false;

    /**
     * Header title.
     */
    protected string $headtitle = '';

    /**
     * Whether to show header.
     */
    protected bool $showHeader = true;

    /**
     * Whether to show footer.
     */
    protected bool $showFooter = true;

    /**
     * Registered commands.
     *
     * @var array<string, callable>
     */
    protected array $_commands = [];

    /**
     * Create a new console application.
     *
     * @param string $name    Application name
     * @param string $version Application version
     * @param string $locale  Application locale
     */
    public function __construct(string $name = '', string $version = '1.0.0', string $locale = 'en')
    {
        parent::__construct($name, $version);

        $this->onException([$this, 'onError']);

        if ($locale !== 'en' && file_exists($path = __DIR__ . "/locales/{$locale}.php")) {
            $this->addLocale($locale, require $path, true);
        }

        $this->defineColors('help_header|help_item_even|help_item_odd', ['fg' => Color::GREEN]);
        $this->defineColors('help_group', ['fg' => Color::fg256(49)]);
        $this->defineColors('help_category', ['fg' => Color::YELLOW]);
        $this->defineColors('help_usage|help_description_even|help_description_odd|help_summary', ['fg' => Color::WHITE]);
    }

    /**
     * Define color styles.
     *
     * @param string $names Style names
     * @param array  $style Style definition
     */
    public function defineColors(string $names, array $style): self
    {
        foreach (explode('|', $names) as $name) {
            Color::style($name, $style);
        }

        return $this;
    }

    /**
     * Set the container instance.
     */
    public function container(ContainerInterface $container): self
    {
        $this->container = $container;

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
     * Set whether to show header.
     *
     * @param bool $showHeader Whether to show header
     */
    public function showHeader(bool $showHeader): self
    {
        $this->showHeader = $showHeader;

        return $this;
    }

    /**
     * Set whether to show footer.
     *
     * @param bool $showFooter Whether to show footer
     */
    public function showFooter(bool $showFooter): self
    {
        $this->showFooter = $showFooter;

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
        $action = $this->_commands[$commandName] ?? null;

        if ($action === null) {
            throw new CommandNotFoundException($commandName);
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
        return !empty($this->_commands[$commandName]) && is_callable($this->_commands[$commandName]);
    }

    /**
     * Handle the application with given arguments.
     *
     * @override
     *
     * @param array<string> $argv Command line arguments
     *
     * @return mixed Execution result
     */
    public function handle(array $argv): mixed
    {
        $this->debug    = in_array('--debug', $argv, true);
        Color::$enabled = !in_array('--no-colors', $argv, true);

        $argv = array_filter($argv, fn ($arg) => !in_array($arg, ['--debug', '--no-colors'], true));

        return parent::handle($argv);
    }

    /**
     * Show default help screen.
     *
     * @override
     */
    public function showDefaultHelp(): mixed
    {
        $writer = $this->io()->writer();

        $header = !$this->showHeader ? '' : (
            $this->headtitle ? "\n{$this->headtitle}" : "\n{$this->name}, " . t('version') . " {$this->version}"
        );

        $footer = !$this->showFooter ? '' : t('Run `<command> --help` for specific help');

        if ($this->logo) {
            $writer->logo($this->logo, true);
        }

        $this->outputHelper()->showCommandsHelp($this->commands(), $header, $footer);

        return ($this->onExit)();
    }

    /**
     * Add multiple commands to the console.
     *
     * @param array<class-string<Command>> $commands Array of command FQCNs
     */
    public function addCommands(array $commands): void
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
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

        $command->action($action);

        $this->_commands[$instance->name] = $action;

        $this->add($command, $instance->alias, false);
    }

    /**
     * Hook executed before command execution.
     *
     * @param bool    $suppress Whether output is suppressed
     * @param Command $command  Command instance
     */
    public function before(bool $suppress, Command $command): void
    {
        // Hook before command execution
    }

    /**
     * Hook executed after command execution.
     *
     * @param bool    $suppress Whether output is suppressed
     * @param Command $command  Command instance
     */
    public function after(bool $suppress, Command $command): void
    {
        // Hook after command execution
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

        if ($this->debug) {
            $this->io()->error($e->getTraceAsString(), true);
        }

        exit($exitCode);
    }
}
