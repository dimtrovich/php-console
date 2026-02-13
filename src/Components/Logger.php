<?php
declare(strict_types=1);

namespace Dimtrovich\Console\Components;


use Ahc\Cli\Output\Writer;
use BadMethodCallException;
use Dimtrovich\Console\Icon;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use RuntimeException;
use Stringable;

use function Ahc\Cli\t;


/**
 * Logger component that combines console output with PSR logging.
 *
 * @package Dimtrovich\Console\Components
 *
 * @method void danger(string|Stringable $message, array $context = [])
 * @method void fail(string|Stringable $message, array $context = [])
 * @method void warn(string|Stringable $message, array $context = [])
 */
class Logger implements LoggerInterface
{
	use IconTrait;
    use LoggerTrait;
	use SingletonTrait;

    /**
     * Mapping between console methods and PSR log levels.
     */
    protected const METHOD_TO_LEVEL = [
        'warn'    => 'warning',
        'danger'  => 'error',
        'success' => 'info',
        'fail'    => 'error',
    ];

    /**
     * Mapping between log levels and console styles.
     */
    protected const LEVEL_STYLES = [
        LogLevel::EMERGENCY => ['style' => 'boldWhiteBgRed', 'icon' => Icon::DANGER],
        LogLevel::ALERT     => ['style' => 'boldWhiteBgRed', 'icon' => Icon::DANGER],
        LogLevel::CRITICAL  => ['style' => 'boldWhiteBgRed', 'icon' => Icon::ERROR],
        LogLevel::ERROR     => ['style' => 'boldWhiteBgRed', 'icon' => Icon::ERROR],
        LogLevel::WARNING   => ['style' => 'boldWhiteBgYellow', 'icon' => Icon::WARNING],
        LogLevel::NOTICE    => ['style' => 'boldWhiteBgCyan', 'icon' => Icon::INFO],
        LogLevel::INFO      => ['style' => 'boldWhiteBgBlue', 'icon' => Icon::INFO],
        LogLevel::DEBUG     => ['style' => 'boldWhiteBgGray', 'icon' => Icon::SECONDARY],
    ];

    /**
     * PSR logger instance.
     */
    protected static ?LoggerInterface $logger;

	/**
	 * Default log prefix
	 */
    protected static string $defaultPrefix = '';

    /**
     * Console writer instance.
     */
    protected Writer $writer;

    /**
     * Prefix to add to all log messages.
     */
    protected string $prefix = '';

    /**
     * Create a new logger instance.
     *
     * @param Writer          $writer    Console writer
     * @param string          $prefix    Optional prefix for log messages
     */
    public function __construct(Writer $writer, string $prefix = '')
    {
        $this->writer = $writer;
        $this->prefix = $prefix;
    }

	/**
	 * Configure the logger with writer, PSR logger and default prefix.
	 *
	 * This method should be called once during application bootstrap.
	 * It creates the singleton instance if not already created and sets
	 * the global PSR logger and default prefix.
	 *
	 * @param Writer          $writer Console writer instance
	 * @param LoggerInterface $logger PSR-3 logger instance
	 * @param string          $prefix Default prefix for all log messages
	 */
	public static function configure(Writer $writer, LoggerInterface $logger, string $prefix): void
	{
		if (self::$instance === null) {
			self::$instance = new self($writer, $prefix);
		}

		self::$logger        = $logger;
		self::$defaultPrefix = $prefix;
	}

	/**
	 * Set the global PSR logger instance and default prefix.
	 *
	 * This method should be called once during application bootstrap.
	 *
	 * @param LoggerInterface $logger The PSR-3 logger instance
	 * @param string          $prefix Default prefix for all log messages
	 */
	public static function setLogger(LoggerInterface $logger, string $prefix): void
	{
		static::$logger = $logger;
		static::$defaultPrefix = $prefix;
	}

	/**
	 * Check if a PSR logger has been set.
	 *
	 * @return bool True if a logger is available
	 */
	public static function hasLogger(): bool
	{
		return isset(static::$logger);
	}

	/**
	 * Get the current prefix for this logger instance.
	 *
	 * Returns the instance-specific prefix if set, otherwise the global default prefix.
	 *
	 * @return string The current prefix (empty string if none)
	 */
	public function prefix(): string
	{
		return $this->prefix ?: static::$defaultPrefix;
	}

    /**
	 * Log a message with a specific level.
	 *
	 * This method implements PSR-3 LoggerInterface. It sends the message to
	 * the configured PSR logger and displays it in the console with appropriate
	 * styling and icons.
	 *
	 * @param mixed           $level   PSR log level
	 * @param string|Stringable $message Log message
	 * @param array           $context Additional context data
	 *
	 * @return void
	 *
	 * @throws RuntimeException If no PSR logger has been configured
	 */
    public function log($level, string|Stringable $message, array $context = []): void
    {
		$this->logWithCustomStyle($level, $message, $context);
	}

    /**
	 * Log a success message (info level with success styling).
	 *
	 * @param string|Stringable $message The log message
	 * @param array             $context Additional context data
	 */
    public function success(string|Stringable $message, array $context = []): void
    {
        $this->logWithCustomStyle(LogLevel::INFO, $message, $context, 'boldWhiteBgGreen', Icon::SUCCESS);
    }

    /**
     * Create a new logger instance with a prefixed namespace.
	 *
	 * This method returns a new logger instance that will prefix all messages
	 * with the given string. Multiple prefixes can be chained:
	 * $logger->withPrefix('APP')->withPrefix('DB')->info('message')
	 * // Output: [APP > DB] message
     *
     * @param string $prefix The prefix to add (e.g., 'DB', 'CACHE')
 	 *
 	 * @return self A new logger instance with the combined prefix
     *
     * @example
     * ```php
     * $logger->withPrefix('DB')->info('Connected');
     * // Console: [DB] Connected
     * // Log file: [DB] Connected
     * ```
     */
    public function withPrefix(string $prefix): self
    {
		$currentPrefix = $this->prefix();
		$newPrefix     = $currentPrefix ? $currentPrefix . ' > ' . $prefix : $prefix;

    	return new self($this->writer, $newPrefix);
	}

	/**
	 * Magic call handler for aliased methods.
	 *
	 * Handles method aliases like `warn()`, `danger()`, `fail()` by mapping them
	 * to their corresponding PSR level methods.
	 *
	 * @param string $name      Method name called
	 * @param array  $arguments Method arguments
	 *
	 * @return mixed Result of the called method
	 *
	 * @throws BadMethodCallException If the method doesn't exist and has no alias
	 *
	 * @example
	 * ```php
	 * $logger->warn('Disk space low'); // Maps to warning()
	 * $logger->danger('Critical error'); // Maps to error()
	 * $logger->success('Done'); // Maps to info() with custom style
	 * ```
	 */
	public function __call(string $name, array $arguments = [])
	{
		$method = static::METHOD_TO_LEVEL[$name] ?? null;
		if ($method !== null && method_exists($this, $method)) {
			return call_user_func_array([$this, $method], $arguments);
		}

		throw new BadMethodCallException(t('Call to undefined method "%s".', [static::class . '::' . $name]));
	}

    /**
     * Log with a custom console style.
     *
     * @param string $level   PSR log level
     * @param string $message Log message
     * @param array  $context Context data
     * @param string $style   Console style to use
     * @param string $icon    Icon to display
     */
    protected function logWithCustomStyle(string $level, string|Stringable $message, array $context, ?string $style = null, ?string $icon = null): void
    {
		if (! static::hasLogger()) {
			throw new RuntimeException('Logger instance is not defined.');
		}

        static::$logger->log($level, $this->prefixMessage($message), $context);

        $this->displayInConsole($level, $message, $context, $style, $icon);
    }

    /**
     * Add prefix to message if set.
     *
     * @param string|Stringable $message
     */
    protected function prefixMessage(string|Stringable $message): string
    {
        if ('' === $prefix = $this->prefix()) {
            return (string) $message;
        }

        return '[' . $prefix . '] ' . $message;
    }

    /**
     * Display the log message in the console.
     *
     * @param string      $level   Log level
     * @param string      $message Message
     * @param array       $context Context
     * @param string|null $style   Optional custom style
     * @param string|null $icon    Optional custom icon
     */
    protected function displayInConsole(string $level, string|Stringable $message, array $context = [], ?string $style = null, ?string $icon = null ): void
	{
        $config = $style === null
            ? (self::LEVEL_STYLES[$level] ?? self::LEVEL_STYLES[LogLevel::INFO])
            : ['style' => $style, 'icon' => $icon ?? Icon::INFO];

        $displayStyle = $config['style'];
        $displayIcon = static::$showDefaultIcons ? ($config['icon'] . ' ') : '';

        $label = strtoupper($level);
        $displayMessage = (string) $message;

        // Add context if present
        if (!empty($context) && static::$showDefaultIcons) {
            $contextStr = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $displayMessage .= " {$contextStr}";
        }

        try {
            $this->writer->{$displayStyle}(" {$displayIcon}{$label} ");
        } catch (\Exception) {
            $this->writer->bold(" {$displayIcon}{$label} ");
        }

        $this->writer->write(' ' . $displayMessage, true);
    }
}
