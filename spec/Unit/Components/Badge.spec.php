<?php

use Dimtrovich\Console\Components\Badge;
use Ahc\Cli\Output\Writer;

use function Kahlan\expect;

describe('Badge', function () {
    beforeAll(function() {
		$this->outputFile = __DIR__ . '/../../output-badge.test';

		if (! is_dir($dirname = pathinfo($this->outputFile, PATHINFO_DIRNAME))) {
			mkdir($dirname);
		}
		file_put_contents($this->outputFile, '', LOCK_EX);
	});

    beforeEach(function () {
        $this->writer = new Writer($this->outputFile);
        $this->badge = new Badge($this->writer);
    });

	afterEach(function() {
		file_put_contents($this->outputFile, '', LOCK_EX);
	});

	afterAll(function() {
		if (file_exists($this->outputFile)) {
			unlink($this->outputFile);
		}
	});

    describe('::instance()', function () {

        it('creates a singleton instance', function () {
            $badge1 = Badge::instance($this->writer);
            $badge2 = Badge::instance($this->writer);

            expect($badge1)->toBe($badge2);
        });
    });

    describe('badge types', function () {

        it('displays info badge', function () {
            expect($this->writer)->toReceive('boldWhiteBgCyan')->once()->with(' INFO ');
            expect($this->writer)->toReceive('write')->once()->with(' Test message', true);

            $result = $this->badge->info('Test message');

            expect($result)->toBe($this->badge);
        });

        it('displays info badge with custom label', function () {
            expect($this->writer)->toReceive('boldWhiteBgCyan')->once()->with(' CUSTOM ');
            expect($this->writer)->toReceive('write')->once()->with(' Test message', true);

            $this->badge->info('Test message', 'CUSTOM');
        });

        it('displays success badge', function () {
            expect($this->writer)->toReceive('boldWhiteBgGreen')->once()->with(' SUCCESS ');
            expect($this->writer)->toReceive('write')->once()->with(' Operation completed', true);

            $this->badge->success('Operation completed');
        });

        it('displays warning badge', function () {
            expect($this->writer)->toReceive('boldWhiteBgYellow')->once()->with(' WARNING ');
            expect($this->writer)->toReceive('write')->once()->with(' Low disk space', true);

            $this->badge->warning('Low disk space');
        });

        it('displays error badge', function () {
            expect($this->writer)->toReceive('boldWhiteBgRed')->once()->with(' ERROR ');
            expect($this->writer)->toReceive('write')->once()->with(' Connection failed', true);

            $this->badge->error('Connection failed');
        });

        it('displays danger badge as alias for error', function () {
            expect($this->writer)->toReceive('boldWhiteBgRed')->once()->with(' DANGER ');
            expect($this->writer)->toReceive('write')->once()->with(' Critical error', true);

            $this->badge->danger('Critical error');
        });

        it('displays primary badge', function () {
            expect($this->writer)->toReceive('boldWhiteBgBlue')->once()->with(' PRIMARY ');
            expect($this->writer)->toReceive('write')->once()->with(' Main action', true);

            $this->badge->primary('Main action');
        });

        it('displays secondary badge', function () {
            expect($this->writer)->toReceive('boldWhiteBgGray')->once()->with(' SECONDARY ');
            expect($this->writer)->toReceive('write')->once()->with(' Secondary info', true);

            $this->badge->secondary('Secondary info');
        });

        it('displays dark badge', function () {
            expect($this->writer)->toReceive('boldWhiteBgBlack')->once()->with(' DARK ');
            expect($this->writer)->toReceive('write')->once()->with(' Dark theme', true);

            $this->badge->dark('Dark theme');
        });

        it('displays light badge', function () {
            expect($this->writer)->toReceive('boldBlackBgWhite')->once()->with(' LIGHT ');
            expect($this->writer)->toReceive('write')->once()->with(' Light theme', true);

            $this->badge->light('Light theme');
        });
    });

    describe('outline badge', function () {

        it('displays outline badge with default color', function () {
            expect($this->writer)->toReceive('boldBlue')->once()->with(' OUTLINE ');
            expect($this->writer)->toReceive('write')->once()->with(' Outline message');

            $this->badge->outline('Outline message');
        });

        it('displays outline badge with custom label', function () {
            expect($this->writer)->toReceive('boldGreen')->once()->with(' CUSTOM ');
            expect($this->writer)->toReceive('write')->once()->with(' Custom outline');

            $this->badge->outline('Custom outline', 'CUSTOM', 'success');
        });

        it('displays outline badge with info color', function () {
            expect($this->writer)->toReceive('boldCyan')->once()->with(' INFO ');
            $this->badge->outline('Info outline', 'INFO', 'info');
        });

        it('displays outline badge with success color', function () {
            expect($this->writer)->toReceive('boldGreen')->once()->with(' SUCCESS ');
            $this->badge->outline('Success outline', 'SUCCESS', 'success');
        });

        it('displays outline badge with warning color', function () {
            expect($this->writer)->toReceive('boldYellow')->once()->with(' WARNING ');
            $this->badge->outline('Warning outline', 'WARNING', 'warning');
        });

        it('displays outline badge with error color', function () {
            expect($this->writer)->toReceive('boldRed')->once()->with(' ERROR ');
            $this->badge->outline('Error outline', 'ERROR', 'error');
        });

        it('displays outline badge with primary color', function () {
            expect($this->writer)->toReceive('boldBlue')->once()->with(' PRIMARY ');
            $this->badge->outline('Primary outline', 'PRIMARY', 'primary');
        });

        it('displays outline badge with secondary color', function () {
            expect($this->writer)->toReceive('boldGray')->once()->with(' SECONDARY ');
            $this->badge->outline('Secondary outline', 'SECONDARY', 'secondary');
        });

        it('displays outline badge with dark color', function () {
            expect($this->writer)->toReceive('boldBlack')->once()->with(' DARK ');
            $this->badge->outline('Dark outline', 'DARK', 'dark');
        });

        it('displays outline badge with light color', function () {
            expect($this->writer)->toReceive('boldWhite')->once()->with(' LIGHT ');
            $this->badge->outline('Light outline', 'LIGHT', 'light');
        });
    });

    describe('pill badge', function () {

        it('displays pill badge with default color', function () {
            expect($this->writer)->toReceive('boldWhiteBgBlue')->once()->with('( PILL )');
            expect($this->writer)->toReceive('write')->once()->with(' Pill message', true);

            $this->badge->pill('Pill message');
        });

        it('displays pill badge with custom label', function () {
            expect($this->writer)->toReceive('boldWhiteBgGreen')->once()->with('( CUSTOM )');
            $this->badge->pill('Custom pill', 'CUSTOM', 'success');
        });

        it('displays pill badge with info color', function () {
            expect($this->writer)->toReceive('boldWhiteBgCyan')->once()->with('( INFO )');
            $this->badge->pill('Info pill', 'INFO', 'info');
        });

        it('displays pill badge with success color', function () {
            expect($this->writer)->toReceive('boldWhiteBgGreen')->once()->with('( SUCCESS )');
            $this->badge->pill('Success pill', 'SUCCESS', 'success');
        });

        it('displays pill badge with warning color', function () {
            expect($this->writer)->toReceive('boldWhiteBgYellow')->once()->with('( WARNING )');
            $this->badge->pill('Warning pill', 'WARNING', 'warning');
        });

        it('displays pill badge with error/danger color', function () {
            expect($this->writer)->toReceive('boldWhiteBgRed')->once()->with('( ERROR )');
            $this->badge->pill('Error pill', 'ERROR', 'error');

            expect($this->writer)->toReceive('boldWhiteBgRed')->once()->with('( DANGER )');
            $this->badge->pill('Danger pill', 'DANGER', 'danger');
        });

        it('displays pill badge with primary color', function () {
            expect($this->writer)->toReceive('boldWhiteBgBlue')->once()->with('( PRIMARY )');
            $this->badge->pill('Primary pill', 'PRIMARY', 'primary');
        });

        it('displays pill badge with secondary color', function () {
            expect($this->writer)->toReceive('boldWhiteBgGray')->once()->with('( SECONDARY )');
            $this->badge->pill('Secondary pill', 'SECONDARY', 'secondary');
        });

        it('displays pill badge with dark color', function () {
            expect($this->writer)->toReceive('boldWhiteBgBlack')->once()->with('( DARK )');
            $this->badge->pill('Dark pill', 'DARK', 'dark');
        });

        it('displays pill badge with light color', function () {
            expect($this->writer)->toReceive('boldBlackBgWhite')->once()->with('( LIGHT )');
            $this->badge->pill('Light pill', 'LIGHT', 'light');
        });
    });

    describe('fluent interface', function () {

        it('returns self for method chaining', function () {
            $result = $this->badge
                ->info('Info')
                ->success('Success')
                ->warning('Warning');

            expect($result)->toBe($this->badge);
        });
    });
});
