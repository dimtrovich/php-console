# BlitzPHP Console

[![Latest Version](https://img.shields.io/packagist/v/blitzphp/console.svg?style=flat-square)](https://packagist.org/packages/blitzphp/console)
[![Total Downloads](https://img.shields.io/packagist/dt/blitzphp/console.svg?style=flat-square)](https://packagist.org/packages/blitzphp/console)
[![License](https://img.shields.io/packagist/l/blitzphp/console.svg?style=flat-square)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/packagist/php-v/blitzphp/console.svg?style=flat-square)](https://php.net)

**English** | [Français](./README-FR.md)

A powerful, feature-rich console application builder for PHP. Built on top of `adhocore/cli`, BlitzPHP Console provides an elegant and intuitive interface for creating command-line tools with advanced features like ASCII art, progress bars, interactive menus, and beautiful output formatting.

## 📦 Installation

```bash
composer require blitzphp/console
```

## 🚀 Quick Start

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use BlitzPHP\Console\Application;
use BlitzPHP\Console\Command;

// Create a simple command
class GreetCommand extends Command
{
    protected string $name = 'greet';
    protected string $description = 'Greet someone';
    protected array $arguments = [
        'name' => ['The name of the person to greet']
    ];

    public function handle()
    {
        $name = $this->argument('name', 'World');
        $this->success("Hello, {$name}!");
        return 0;
    }
}

// Create and run the application
$app = Application::create('My CLI Tool', '1.0.0')
    ->withCommands([GreetCommand::class])
    ->run();

exit($app);
```

## 📚 Table of Contents

- [Core Concepts](#core-concepts)
- [Creating Commands](#creating-commands)
- [Input Handling](#input-handling)
- [Output Formatting](#output-formatting)
- [Advanced Features](#advanced-features)
- [Components](#components)
- [ASCII Art](#ascii-art)
- [Themes and Styling](#themes-and-styling)
- [Internationalization (i18n)](#internationalization-i18n)
- [Application Configuration](#application-configuration)
- [Logging Integration](#logging-integration)
- [Testing](#testing)
- [API Reference](#api-reference)

---

## Core Concepts

BlitzPHP Console is built around several key concepts:

- **Application**: The main entry point that manages commands and configuration
- **Command**: Individual executable tasks with arguments and options
- **Input/Output**: Rich interaction with the user through various methods
- **Components**: Reusable UI elements like alerts, badges, and progress bars

## Creating Commands

### Basic Command Structure

```php
<?php

use BlitzPHP\Console\Command;

class UserCommand extends Command
{
    protected string $name = 'user:create';
    protected string $group = 'User Management';
    protected string $description = 'Create a new user';
    protected string $alias = 'u:c';
    protected string $version = '1.0.0';
    protected string $usage = 'user:create [options] [--] <name> <email>';

    protected array $arguments = [
        'name'  => ['The user\'s full name'],
        'email' => ['The user\'s email address', 'default@example.com']
    ];

    protected array $options = [
        '--admin'  => ['Give admin privileges', false],
        '--role'   => ['User role', 'user', 'strval'],
        '--active' => ['Activate user', true, 'boolval']
    ];

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $isAdmin = $this->option('admin');
        
        $this->info("Creating user: {$name} ({$email})");
        
        if ($isAdmin) {
            $this->warn('This user will have admin privileges');
        }
        
        // Your logic here
        
        $this->success('User created successfully!');
        return 0;
    }
}
```

### Command Lifecycle

```php
class LifecycleCommand extends Command
{
    protected function configure(BaseCommand $command): void
    {
        // Called during initialization
        // Configure command options/arguments
    }

    public function initialize(Console $app): BaseCommand
    {
        // Called before handle()
        // Set up dependencies
        return parent::initialize($app);
    }

    public function handle()
    {
        // Main execution logic
    }

    // Optional: Define custom validation
    protected function validate(): void
    {
        if ($this->argument('name') === 'admin') {
            throw new \InvalidArgumentException('Name cannot be "admin"');
        }
    }
}
```

## Input Handling

### Basic Prompts

```php
class InteractiveCommand extends Command
{
    public function handle()
    {
        // Simple prompt
        $name = $this->ask('What is your name?', 'Guest');
        
        // Hidden input (password)
        $password = $this->secret('Enter your password:');
        
        // Confirmation
        if ($this->confirm('Do you want to continue?', 'y')) {
            $this->info('Continuing...');
        }
        
        // Choice from options
        $color = $this->choice(
            'Favorite color?',
            ['red' => 'Red', 'blue' => 'Blue', 'green' => 'Green'],
            'blue'
        );
        
        // Multiple choices
        $colors = $this->choices(
            'Select colors:',
            ['r' => 'Red', 'g' => 'Green', 'b' => 'Blue']
        );
        
        // Auto-completion
        $country = $this->askWithCompletion(
            'Country:',
            ['USA', 'Canada', 'Mexico', 'Brazil'],
            'USA'
        );
    }
}
```

### Advanced Input Validation

```php
class ValidatedCommand extends Command
{
    public function handle()
    {
        $email = $this->prompt('Email:', null, function($input) {
            if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Invalid email format');
            }
            return strtolower($input);
        }, 3); // 3 retry attempts
        
        $age = $this->prompt('Age:', 18, function($input) {
            $age = (int) $input;
            if ($age < 18 || $age > 120) {
                throw new \InvalidArgumentException('Age must be between 18 and 120');
            }
            return $age;
        });
    }
}
```

## Output Formatting

### Basic Output Methods

```php
class OutputCommand extends Command
{
    public function handle()
    {
        $this->write('Simple text');
        $this->line('Text with new line');
        $this->newLine(); // Empty line
        
        $this->info('Informational message');
        $this->success('Operation completed');
        $this->warn('Warning message');
        $this->error('Something went wrong');
        $this->comment('Just a comment');
        $this->question('What is your name?');
        
        $this->note('Important note');
        $this->notice('Notice something');
        $this->caution('Be careful!');
        $this->debug('Variable value: ' . $var);
        $this->fail('Task failed');
    }
}
```

### Colored Output

```php
class ColorCommand extends Command
{
    public function handle()
    {
        // Basic colors
        $this->red('Error in red');
        $this->green('Success in green');
        $this->blue('Info in blue');
        $this->yellow('Warning in yellow');
        $this->magenta('Magenta text');
        $this->cyan('Cyan text');
        $this->gray('Gray text');
        $this->purple('Purple text');
        $this->indigo('Indigo text');
        
        // Text styles
        $this->bold('Bold text');
        $this->italic('Italic text');
        $this->underline('Underlined text');
        $this->strike('Strikethrough text');
        
        // Custom color
        $this->colorize('Custom color', 'bright-red');
        
        // With newline
        $this->green('Success with newline', true);
    }
}
```

### Tables

```php
class TableCommand extends Command
{
    public function handle()
    {
        $users = [
            ['John Doe', 30, 'New York'],
            ['Jane Smith', 25, 'London'],
            ['Bob Johnson', 35, 'Paris']
        ];
        
        // Simple table
        $this->table(['Name', 'Age', 'City'], $users);
    }
}
```

**Output:**
```
Name       Age  City
John Doe   30   New York
Jane Smith 25   London
Bob Johnson 35  Paris
```

### Grid Display

```php
class GridCommand extends Command
{
    public function handle()
    {
        $data = [
            ['Product', 'Price', 'Stock', 'Status'],
            ['Laptop', 999.99, 15, 'In Stock'],
            ['Mouse', 29.99, 42, 'In Stock'],
            ['Keyboard', 79.99, 8, 'Low Stock'],
            ['Monitor', 299.99, 0, 'Out of Stock']
        ];
        
        $this->grid($data);
    }
}
```

**Output:**
```
Product   Price   Stock  Status      
Laptop    999.99  15     In Stock    
Mouse     29.99   42     In Stock    
Keyboard  79.99   8      Low Stock   
Monitor   299.99  0      Out of Stock
```

### JSON Output

```php
class JsonCommand extends Command
{
    public function handle()
    {
        $data = [
            'user' => [
                'name' => 'John',
                'email' => 'john@example.com',
                'roles' => ['admin', 'editor']
            ]
        ];
        
        $this->json($data); // Pretty printed JSON
    }
}
```

**Output:**
```json
{
    "user": {
        "name": "John",
        "email": "john@example.com",
        "roles": [
            "admin",
            "editor"
        ]
    }
}
```

## Advanced Features

### Progress Bars

```php
class ProgressCommand extends Command
{
    public function handle()
    {
        // With array
        $items = range(1, 100);
        $this->withProgressBar($items, function($item, $bar) {
            // Process item
            usleep(50000); // Simulate work
        });
        
        // With total count
        $this->withProgressBar(50, function($bar) {
            for ($i = 0; $i < 50; $i++) {
                // Do work
                usleep(50000);
                $bar->advance();
            }
        });
        
        // Manual progress bar
        $bar = $this->progress(100);
        for ($i = 0; $i < 100; $i++) {
            $bar->advanceWithMessage(1, "Processing item {$i}");
            usleep(50000);
        }
        $bar->finish();
        $bar->showStats(); // Show elapsed time and speed
    }
}
```

**Output:**
```
[████████░░░░░░░░░░░░░░░░░░░░░] 25%
Statistics: 100 items in 5.23s (19.12 items/s)
Messages:
  • Processing item 0
  • Processing item 25
  • Processing item 50
  • Processing item 75
```

### Spinners

```php
class SpinnerCommand extends Command
{
    public function handle()
    {
        $result = $this->withSpinner(function() {
            // Long running task
            sleep(3);
            return 'Task completed';
        }, 'Processing long task...');
        
        $this->success($result);
    }
}
```

**Animation:**
```
Processing long task... ⠋
Processing long task... ⠙
Processing long task... ⠹
Processing long task... ✓
```

### Timelines

```php
class TimelineCommand extends Command
{
    public function handle()
    {
        $events = [
            ['status' => 'completed', 'description' => 'Database migrated'],
            ['status' => 'processing', 'description' => 'Cache cleared'],
            ['status' => 'failed', 'description' => 'Assets compiled'],
            ['description' => 'Waiting for server'] // pending by default
        ];
        
        $this->timeline($events);
    }
}
```

**Output:**
```
Timeline:
  ✓ Database migrated
  ↻ Cache cleared
  ✗ Assets compiled
  ○ Waiting for server
```

### Charts

```php
class ChartCommand extends Command
{
    public function handle()
    {
        $data = ['Linux' => 50, 'Windows' => 30, 'Mac' => 20];
        
        // Bar chart
        $this->chart($data, 'bar');
        
        // Pie chart
        $this->chart($data, 'pie');
    }
}
```

**Bar Chart Output:**
```
Linux                ████████████████████████████████████████████ 50
Windows              ████████████████████████████████ 30
Mac                  ████████████████████ 20
```

**Pie Chart Output:**
```
Pie Chart
  Linux: 50.0%
  Windows: 30.0%
  Mac: 20.0%
```

### Heatmaps

```php
class HeatmapCommand extends Command
{
    public function handle()
    {
        $temperatures = [10, 20, 5, 30, 15, 25, 18];
        
        $this->heatmap($temperatures);
        
        // Custom density characters
        $this->heatmap($temperatures, ['▁', '▂', '▃', '▄', '▅', '▆', '▇', '█']);
    }
}
```

**Output:**
```
░▒▓█▒▓░
▂▄▁█▃▅▄
```

### Interactive Menus

```php
class MenuCommand extends Command
{
    public function handle()
    {
        $options = [
            '1' => ['label' => 'Create user', 'action' => 'create'],
            '2' => ['label' => 'Delete user', 'action' => 'delete'],
            '3' => ['label' => 'List users', 'action' => 'list'],
            'q' => 'Quit'
        ];
        
        $choice = $this->menu('User Management', $options, '1');
        
        switch ($choice['action'] ?? $choice) {
            case 'create':
                $this->call('user:create');
                break;
            case 'delete':
                $this->call('user:delete');
                break;
            case 'list':
                $this->call('user:list');
                break;
            case 'Quit':
                return 0;
        }
    }
}
```

**Output:**
```
User Management
  1 Create user
  2 Delete user
  3 List users
  q Quit
Choose an option: 2
```

### Animations

```php
class AnimationCommand extends Command
{
    public function handle()
    {
        $frames = ['◐', '◓', '◑', '◒'];
        $this->animation($frames, 5, 100000); // 5 iterations, 100ms delay
    }
}
```

## Components

### Alerts

```php
use BlitzPHP\Console\Components\Alert;

class AlertCommand extends Command
{
    public function handle()
    {
        $alert = $this->alert();
        
        $alert->info('System is running', 'System Status');
        $alert->success('Operation completed successfully');
        $alert->warning('Low disk space', 'Warning');
        $alert->error('Database connection failed', 'Error');
        $alert->danger('Critical error', 'DANGER');
        $alert->primary('Maintenance scheduled', 'MAINTENANCE');
        $alert->secondary('Additional information');
    }
}
```

**Output:**
```
************************
*    SYSTEM STATUS     *
*  System is running   *
************************

**********************
*      SUCCESS       *
* Operation completed*
**********************
```

### Badges

```php
use BlitzPHP\Console\Components\Badge;

class BadgeCommand extends Command
{
    public function handle()
    {
        $badge = $this->badge();
        
        $badge->info('System is running', 'SYSTEM');
        $badge->success('Task completed', 'DONE');
        $badge->warning('Low memory', 'WARN');
        $badge->error('Connection failed', 'ERROR');
        
        // Outline badges
        $badge->outline('Outline message', 'OUTLINE', 'blue');
        
        // Pill badges
        $badge->pill('Pill message', 'PILL', 'info');
    }
}
```

**Output:**
```
[SYSTEM] System is running
[DONE] Task completed
(WARN) Low memory
(INFO) Pill message
```

### Icons

```php
use BlitzPHP\Console\Icon;

class IconCommand extends Command
{
    public function handle()
    {
        $this->alert()->success('User created', 'USER', Icon::USER);
        $this->badge()->info('Download complete', 'FILE', Icon::DOWNLOAD);
    }
}
```

**Output with icons:**
```
👤 USER
📁 Download complete
```

## ASCII Art

```php
use BlitzPHP\Console\Traits\AsciiArt;

class AsciiCommand extends Command
{
    use AsciiArt; // Optional feature
    
    public function handle()
    {
        // Basic ASCII art
        $this->asciiArt('WELCOME');
        
        // With specific font
        $this->asciiArt('HELLO', 'big');
        
        // Chain with font selection
        $this->withFont('starwars')->asciiArt('FORCE');
        
        // Create banner
        $this->banner('IMPORTANT', '*', 'big');
        
        // Font management
        $fonts = $this->getAvailableFonts(); // ['standard', 'minimal']
        $this->previewFont('standard', 'ABCD');
        
        // Register custom font
        $this->registerFont('custom', [
            'A' => '  ▲▲  ',
            'B' => ' ■■ ',
            // ...
        ]);
        
        // Load fonts from directory
        $this->loadFonts(__DIR__ . '/fonts/');
    }
}
```

**Output (standard font):**
```
  ██    ██  █████  ██
█   █  █  █  █     ██
```

**Banner output:**
```
********************
*    IMPORTANT     *
********************
```

## Themes and Styling

BlitzPHP Console comes with 8 built-in themes, each carefully designed for different environments and preferences.

### Available Themes

| Theme | Description | Preview |
|-------|-------------|---------|
| `default` | Original adhocore/cli styling | Classic, balanced colors |
| `light` | Optimized for light terminal backgrounds | Darker colors on light background |
| `dark` | Optimized for dark terminal backgrounds | High contrast, easy on eyes |
| `solarized` | Ethan Schoonover's popular color scheme | Perfect for long coding sessions |
| `monokai` | Vibrant syntax highlighting theme | Popular among developers |
| `nord` | Arctic, north-bluish color palette | Clean and calm |
| `dracula` | Dark theme with vibrant colors | Eye-catching palette |
| `github` | Familiar GitHub interface colors | Clean and professional |

### Theme Examples

**Solarized Theme:**
```
$ php console user:list
Arguments
  <name> User's full name
Options
  --admin Give admin privileges
  --role  User role [default: user]
```

**Dracula Theme:**
```
$ php console db:migrate
✓ Database migrated successfully
⚠ Cache cleared
✗ Failed to compile assets
```

**Nord Theme:**
```
$ php console server:start
ℹ Server started on http://localhost:8000
✓ Document root: /var/www/public
⚠ PHP built-in server (development)
```

### Applying Themes

```php
$app = Application::create('MyApp', '1.0.0')
    ->withTheme('dark')      // Dark theme
    ->withTheme('light')     // Light theme
    ->withTheme('solarized') // Solarized theme
    ->withTheme('monokai')   // Monokai theme
    ->withTheme('nord')      // Nord theme
    ->withTheme('dracula')   // Dracula theme
    ->withTheme('github');   // GitHub theme
```

### Custom Styles

You can also define custom color styles programmatically:

```php
$app->withStyles([
    'help_header' => ['fg' => 'green', 'bold' => 1],
    'error'       => ['fg' => 'red', 'bg' => 'black'],
    'custom_blue' => ['fg' => 69], // 256 color
    'success_bg'  => ['fg' => 'white', 'bg' => 'green'],
]);
```

### 256-Color Support

All themes leverage 256-color support for richer, more precise colors:

```php
// Using 256-color codes
'magenta' => ['fg' => Color::fg256(201)], // Bright magenta
'indigo'  => ['fg' => Color::fg256(54)],  // Deep indigo
'orange'  => ['fg' => Color::fg256(214)], // Vibrant orange
```

## Internationalization (i18n)

BlitzPHP Console supports multiple languages with built-in translations for French and extensible translation system for other languages.

### Built-in Locales

| Locale | Language | File |
|--------|----------|------|
| `en` | English | (built-in) |
| `fr` | French | `fr.php` |

### Setting the Locale

```php
$app = Application::create('MyApp', '1.0.0')
    ->withLocale('fr'); // Use French translations
```

### French Translations Example

When using French locale, all built-in messages are automatically translated:

**English output:**
```
Command not found
Did you mean help?
Choose an option:
```

**French output:**
```
Commande non trouvée
Vouliez-vous dire help ?
Choisissez une option :
```

### Custom Translations

You can add your own translations or override existing ones:

```php
$app->withTranslations('fr', [
    'Hello %s' => 'Bonjour %s',
    'Goodbye'  => 'Au revoir',
    'Welcome to %s' => 'Bienvenue sur %s',
], true); // true = set as default locale
```

### Translation Function

The package uses a simple translation function `t()` that's available globally:

```php
// In your command
$this->line(t('Hello %s', [$name]));
$this->error(t('Command %s not found', [$commandName]));
```

### Creating New Translations

To add support for a new language, create a PHP file in your project and load it:

```php
// es.php
<?php
return [
    'Hello %s' => 'Hola %s',
    'Goodbye'  => 'Adiós',
];

// In your application
$app->withTranslations('es', require 'es.php', true);
```

## Application Configuration

### Basic Setup

```php
use BlitzPHP\Console\Application;

$app = Application::create('My Console App', '2.1.0')
    ->withLocale('fr')                       // Use French translations
    ->withTheme('dracula')                   // Use Dracula theme
    ->withIcons(true, false, true)           // Configure default icons
    ->withLogo("                             // ASCII logo
   _____ _ _ _        _____ _    _ _____
  |  ___) (_) |      / ____| |  | |  __ \
  | |__ | ||_ _| | | |    | |__| | |__) |
  ")
    ->withHeadTitle('My CLI Tool v2')        // Custom header
    ->withoutHeadTitle()                      // No header
    ->withFooter()                            // Show help footer
    ->withDebug()                             // Enable debug mode
    ->withHooks(
        before: function($suppress, $cmd) {   // Before each command
            $this->comment("Starting: {$cmd->name()}");
        },
        after: function($suppress, $cmd) {    // After each command
            $this->comment("Finished: {$cmd->name()}");
        }
    )
    ->withContainer($container)                // DI container
    ->withExceptionHandler(function($e, $code) { // Custom error handler
        $this->error("Oops: {$e->getMessage()}");
        exit($code);
    })
    ->withCommands([
        MakeCommand::class,
        ServeCommand::class,
        RouteListCommand::class,
    ])
    ->withDefaultCommand('help');               // Default command
```

### Icons Configuration

Control default icons behavior globally:

```php
$app->withIcons(
    alert: true,   // Enable default icons for alerts
    badge: false,  // Disable default icons for badges
    logger: true   // Enable default icons for logger
);
```

Individual calls can override:
```php
$this->alert()->success('Done', 'SUCCESS', Icon::STAR); // Force star icon
$this->badge()->info('Message', 'INFO', false);        // No icon for this badge
```

### Running the Application

```php
// Standard run
$exitCode = $app->run();
exit($exitCode);

// With custom arguments
$app->run(['console', 'user:create', 'John', '--admin']);

// Handle command line flags
// php myapp.php --debug      # Enable debug mode
// php myapp.php --no-colors   # Disable colors
```

## Logging Integration

### Basic Logging

```php
class LoggingCommand extends Command
{
    public function handle()
    {
        // Log with default prefix
        $this->log()->info('User logged in', ['user_id' => 123]);
        $this->log()->error('Database connection failed');
        $this->log()->warning('Low disk space');
        
        // Success (info level with green styling)
        $this->log()->success('Operation completed');
        
        // Aliases
        $this->log()->warn('Deprecated method');   // Maps to warning
        $this->log()->danger('Critical error');    // Maps to error
        $this->log()->fail('Task failed');         // Maps to error
        
        // With context
        $this->log()->info('User action', [
            'user' => 'john',
            'action' => 'login',
            'ip' => '127.0.0.1'
        ]);
    }
}
```

**Console output:**
```
ℹ INFO  User logged in
✓ SUCCESS Operation completed
⚠ WARNING Deprecated method
✗ ERROR  Critical error
```

### Prefixing Logs

```php
class DatabaseCommand extends Command
{
    public function handle()
    {
        // Create prefixed logger
        $dbLog = $this->log('DB');
        $dbLog->info('Connecting to database');
        
        // Chain prefixes
        $this->log('APP')
            ->withPrefix('CACHE')
            ->info('Cache cleared');
        // Output: [APP > CACHE] Cache cleared
        
        // Different prefixes create different instances
        $logger1 = $this->log('DB');
        $logger2 = $this->log('CACHE');
        // $logger1 !== $logger2
    }
}
```

**Console output:**
```
[DB] Connecting to database
[APP > CACHE] Cache cleared
```

### Configuration

```php
// In your application bootstrap
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$monolog = new Logger('console');
$monolog->pushHandler(new StreamHandler('logs/console.log'));

$app = Application::create('MyApp')
    ->withLogger($monolog, 'APP') // All logs prefixed with [APP]
    ->withCommands([...])
    ->run();
```

### PSR-3 Compatibility

The Logger component fully implements `Psr\Log\LoggerInterface`, so it can be used anywhere a PSR-3 logger is expected:

```php
class MyService
{
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function doSomething()
    {
        $this->logger->info('Doing something');
    }
}

// In your command
$service = new MyService($this->log());
```

## Advanced Command Features

### Calling Other Commands

```php
class MainCommand extends Command
{
    public function handle()
    {
        // Call by name
        $result = $this->call('user:create', [
            'name' => 'John',
            'email' => 'john@example.com'
        ], ['--admin' => true]);
        
        // Call by class name
        $result = $this->call(UserCreateCommand::class, [
            'name' => 'Jane'
        ]);
        
        // Check if command exists
        if ($this->commandExists('migrate')) {
            $this->call('migrate');
        }
    }
}
```

### Silent Execution

```php
// Execute without output
$result = $app->callSilent('cache:clear');

// Capture output as string
$output = $app->captureOutput('user:list', ['--active' => true]);

// Check if command was executed
if ($app->hasExecuted('migrate')) {
    $this->info('Migration already run');
}

// Clear output cache
$app->clearOutputCache();          // Clear all
$app->clearOutputCache('user:list'); // Clear specific command
```

### Parameter Handling

```php
class ParameterCommand extends Command
{
    protected array $arguments = [
        'id' => ['User ID'],
        'name' => ['User name']
    ];
    
    protected array $options = [
        '--force' => ['Force operation', false]
    ];
    
    public function handle()
    {
        // Get specific argument
        $id = $this->argument('id');
        
        // Get with default
        $name = $this->argument('name', 'Guest');
        
        // Check if argument exists
        if ($this->hasArgument('id')) {
            // ...
        }
        
        // Get all arguments
        $allArgs = $this->arguments();
        
        // Get option
        $force = $this->option('force');
        
        // Get all options
        $allOpts = $this->options();
        
        // Get any parameter (argument or option)
        $value = $this->parameter('id');
        
        // Set parameters (usually done by the system)
        $this->setParameters(['id' => 123], ['force' => true]);
    }
}
```

### System Integration

```php
class SystemCommand extends Command
{
    public function handle()
    {
        // Send desktop notification
        $this->notify('Task Complete', 'Backup finished');
        
        // Play beep sound
        $this->beep(3); // Beep three times
        
        // Clear screen
        $this->clearScreen();
        
        // Get terminal info
        $width = $this->terminal->width();
        $height = $this->terminal->height();
        
        // Center text
        $this->center('Welcome');
        
        // Justify text
        $this->justify('Left text', 'Right text');
        
        // Add border
        $this->border(50, '=');
    }
}
```

**Output:**
```
===================== Welcome =====================
Left text                                       Right text
```

### Cursor Control

```php
class CursorCommand extends Command
{
    public function handle()
    {
        // Hide/show cursor
        $this->write($this->cursor->hide());
        // ... do something
        $this->write($this->cursor->show());
        
        // Move cursor
        $this->write($this->cursor->up(2));
        $this->write($this->cursor->down(1));
        $this->write($this->cursor->left(5));
        $this->write($this->cursor->right(3));
        
        // Position
        $this->write($this->cursor->position(10, 5));
        
        // Save/restore
        $this->write($this->cursor->save());
        // ... move around
        $this->write($this->cursor->restore());
    }
}
```

## Testing

### Console Output Testing

```php
use Tests\Helpers\ConsoleOutput;

class MyCommandTest extends TestCase
{
    public function testCommandOutput()
    {
        ConsoleOutput::setUp();
        
        $app = Application::create('Test');
        $app->withCommands([MyCommand::class]);
        $app->call('my:command');
        
        $output = ConsoleOutput::buffer();
        $this->assertStringContainsString('Success', $output);
        
        ConsoleOutput::tearDown();
    }
}
```

### Mocking Input

```php
public function testInteractiveCommand()
{
    $interactor = $this->createMock(Interactor::class);
    $interactor->method('prompt')->willReturn('John');
    
    $command = new InteractiveCommand();
    $command->initialize($this->app);
    $command->setInteractor($interactor);
    
    $result = $command->handle();
    $this->assertEquals(0, $result);
}
```

## API Reference

### Command Class

| Method | Description |
|--------|-------------|
| `handle()` | Main command logic (abstract) |
| `argument(string $name, $default = null)` | Get argument value |
| `arguments()` | Get all arguments |
| `hasArgument(string $name)` | Check if argument exists |
| `option(string $name, $default = null)` | Get option value |
| `options()` | Get all options |
| `hasOption(string $name)` | Check if option exists |
| `parameter(string $name, $default = null)` | Get argument or option |
| `call(string $command, array $args = [], array $opts = [])` | Call another command |
| `commandExists(string $name)` | Check if command exists |
| `pad(string $item, int $max, int $extra = 2, int $indent = 0)` | Pad string for alignment |

### Input Methods

| Method | Description |
|--------|-------------|
| `ask(string $question, $default = null)` | Ask for input |
| `secret(string $text, callable $fn = null, int $retry = 3)` | Ask for hidden input |
| `confirm(string $question, string $default = 'y')` | Ask for confirmation |
| `choice(string $question, array $choices, $default = null, bool $case = false)` | Single choice |
| `choices(string $question, array $choices, $default = null, bool $case = false)` | Multiple choices |
| `askWithCompletion(string $question, array $choices, $default = null)` | Auto-completion |
| `prompt(string $text, $default = null, callable $fn = null, int $retry = 3)` | Prompt with validation |

### Output Methods

| Method | Description |
|--------|-------------|
| `write(string $text, bool $eol = false)` | Write text |
| `line(string $message, ?string $color = null)` | Write line with optional color |
| `newLine()` | Add empty line |
| `eol(int $n = 1)` | Add end of lines |
| `info(string $message)` | Info message |
| `success(string $message)` | Success message |
| `warn(string $message)` | Warning message |
| `error(string $message)` | Error message |
| `comment(string $message)` | Comment |
| `question(string $message)` | Question |
| `note(string $message)` | Note |
| `notice(string $message)` | Notice |
| `caution(string $message)` | Caution |
| `debug(string $message)` | Debug |
| `fail(string $message)` | Fail |
| `colorize(string $message, string $style, bool $eol = false)` | Colorize text |
| `bold(string $message, bool $eol = false)` | Bold text |
| `italic(string $message, bool $eol = false)` | Italic text |
| `underline(string $message, bool $eol = false)` | Underline |
| `strike(string $message, bool $eol = false)` | Strikethrough |
| `red(string $message, bool $eol = false)` | Red text |
| `green(string $message, bool $eol = false)` | Green text |
| `blue(string $message, bool $eol = false)` | Blue text |
| `yellow(string $message, bool $eol = false)` | Yellow text |
| `table(array $headers, array $rows = [], array $styles = [])` | Display table |
| `json($data)` | Display JSON |
| `bulletList(array $items, string $title = '', string $color = 'yellow')` | Bullet list |
| `numberedList(array $items, string $title = '', string $color = 'yellow')` | Numbered list |
| `border(?int $length = null, string $char = '-')` | Border line |
| `center(string $text, array $options = [])` | Center text |
| `justify(string $first, ?string $second = '', array $options = [])` | Justify text |

### Advanced Features

| Method | Description |
|--------|-------------|
| `wait(int $seconds, bool $countdown = false, string $waitMsg = '...')` | Wait |
| `pause(string $message = 'Press any key...')` | Pause |
| `withSpinner(callable $callback, string $message = 'Processing...')` | Spinner |
| `withProgressBar(iterable\|int $items, callable $callback)` | Progress bar |
| `liveCounter(callable $updater, int $step = 10, string $label = 'Counter', int $interval = 1000000)` | Live counter |
| `timeline(array $events)` | Timeline |
| `heatmap(array $data, array $colors = ['░', '▒', '▓', '█'])` | Heatmap |
| `grid(array $data, ?callable $formatter = null)` | Grid |
| `chart(array $data, string $type = 'bar', int $height = 10)` | Chart |
| `menu(string $title, array $options, ?string $default = null)` | Menu |
| `animation(array $frames, int $iterations = 3, int $delay = 100000)` | Animation |
| `beep(int $count = 1)` | Beep |
| `notify(string $title, string $message)` | Notification |
| `task(string $task, ?int $sleep = null)` | Task |
| `counter(int $start = 0, int $end = 100, int $step = 1)` | Counter |

### Components

| Class | Description |
|-------|-------------|
| `Alert` | Alert component |
| `Badge` | Badge component |
| `Logger` | PSR-3 logger with console output |
| `ProgressBar` | Progress bar |
| `Icon` | Icon constants |

### Application Methods

| Method | Description |
|--------|-------------|
| `create(string $name, string $version = '1.0.0')` | Create application |
| `withLocale(string $locale)` | Set locale |
| `withTranslations(string $locale, array $translations, bool $default = false)` | Add translations |
| `withTheme(string $theme)` | Apply theme |
| `withStyles(array $styles)` | Apply custom styles |
| `withIcons(?bool $alert = null, ?bool $badge = null, ?bool $logger = null)` | Configure icons |
| `withLogo(string $logo)` | Set ASCII logo |
| `withHeadTitle(string $headtitle)` | Set header title |
| `withoutHeadTitle()` | Disable header |
| `withFooter()` | Enable footer |
| `withDebug(bool $debug = true)` | Enable debug |
| `withHooks(?callable $before = null, ?callable $after = null)` | Set hooks |
| `withContainer(ContainerInterface $container)` | Set DI container |
| `withExceptionHandler(callable $handler)` | Set exception handler |
| `withLogger(LoggerInterface $logger, string $prefix = '')` | Set PSR logger |
| `withCommands(array $commands)` | Register commands |
| `withDefaultCommand(string $command)` | Set default command |
| `run(array $argv = [])` | Run application |

---

## 📄 License

The BlitzPHP Console package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---
