<?php

use BlitzPHP\Console\Application;
use BlitzPHP\Console\Console;
use BlitzPHP\Contracts\Container\ContainerInterface;
use Kahlan\Plugin\Double;

use function Kahlan\expect;

describe('Application', function () {

    beforeEach(function () {
        $this->app = Application::create('Test App', '1.0.0');
    });

    describe('::create()', function () {

        it('creates a new application instance with fluent interface', function () {
            $app = Application::create('My CLI', '2.0.0');

            expect($app)->toBeAnInstanceOf(Application::class);
        });
    });

    describe('configuration methods', function () {

        it('configures locale with built-in translations', function () {
            $result = $this->app->withLocale('fr');

            expect($result)->toBe($this->app);
        });

        it('configures custom translations', function () {
            $translations = ['Hello' => 'Bonjour'];

            $result = $this->app->withTranslations('fr', $translations, true);

            expect($result)->toBe($this->app);
        });

        it('configures application logo', function () {
            $logo = "  _   _   _   _  ";

            $result = $this->app->withLogo($logo);

            expect($result)->toBe($this->app);
        });

        it('configures head title', function () {
            $result = $this->app->withHeadTitle('Custom Title');

            expect($result)->toBe($this->app);
        });

        it('disables head title', function () {
            $result = $this->app->withoutHeadTitle();

            expect($result)->toBe($this->app);
        });

        it('enables footer', function () {
            $result = $this->app->withFooter();

            expect($result)->toBe($this->app);
        });
    });

    describe('error handling', function () {

        it('configures debug mode', function () {
            $result = $this->app->withDebug(true);

            expect($result)->toBe($this->app);
        });

        it('configures custom exception handler', function () {
            $handler = function ($e, $code) {
                echo 'Custom error';
            };

            $result = $this->app->withExceptionHandle($handler);

            expect($result)->toBe($this->app);
        });
    });

    describe('hooks and container', function () {

        it('configures before/after hooks', function () {
            $before = function () {};
            $after = function () {};

            $result = $this->app->withHooks($before, $after);

            expect($result)->toBe($this->app);
        });

        it('configures container', function () {
            $container = Double::instance(['implements' => [ContainerInterface::class]]);

            $result = $this->app->withContainer($container);

            expect($result)->toBe($this->app);
        });
    });

    describe('command registration', function () {

        it('registers multiple commands', function () {
            $commands = [
                Tests\Fixtures\CommadOne::class,
                Tests\Fixtures\CommadTwo::class,
            ];

            $result = $this->app->withCommands($commands);

            expect($result)->toBe($this->app);
        });
    });

    describe('command execution', function () {

        beforeEach(function () {
            // Create a mock command for tests
            $this->mockCommandClass = new class extends \BlitzPHP\Console\Command {
                protected string $name = 'test:command';
                protected string $description = 'Test command';
                public function handle() {
                    $this->writer->write('Command executed');
                    return 0;
                }
            };

            $this->app->withCommands([get_class($this->mockCommandClass)]);
        });
    });
});
