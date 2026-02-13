<?php

use BlitzPHP\Console\Components\IconTrait;
use BlitzPHP\Console\Icon;


describe('Components / IconTrait', function () {

    beforeEach(function () {
        $this->traitClass = new class {
            use IconTrait;

            public static function reset() {
                self::$showDefaultIcons = false;
            }
        };

        $this->traitClass::reset();
    });

    describe('global icon settings', function () {

        it('disables default icons by default', function () {
            expect($this->traitClass::defaultIconsEnabled())->toBe(false);
        });

        it('enables default icons globally', function () {
            $this->traitClass::showDefaultIcons(true);

            expect($this->traitClass::defaultIconsEnabled())->toBe(true);
        });

        it('disables default icons globally', function () {
            $this->traitClass::showDefaultIcons(true);
            $this->traitClass::showDefaultIcons(false);

            expect($this->traitClass::defaultIconsEnabled())->toBe(false);
        });
    });

    describe('icon resolution', function () {

        beforeEach(function () {
            $this->resolver = new class {
                use IconTrait;

                public function resolve($icon, $default) {
                    return $this->resolveIcon($icon, $default);
                }
            };
        });

        it('returns null when icon is false', function () {
            $result = $this->resolver->resolve(false, Icon::INFO);

            expect($result)->toBe(null);
        });

        it('returns provided icon string', function () {
            $result = $this->resolver->resolve('★', Icon::INFO);

            expect($result)->toBe('★');
        });

        context('with default icons disabled', function () {

            beforeEach(function () {
                $this->resolver::showDefaultIcons(false);
            });

            it('returns null when icon is null', function () {
                $result = $this->resolver->resolve(null, Icon::INFO);

                expect($result)->toBe(null);
            });
        });

        context('with default icons enabled', function () {

            beforeEach(function () {
                $this->resolver::showDefaultIcons(true);
            });

            it('returns default icon when icon is null', function () {
                $result = $this->resolver->resolve(null, Icon::INFO);

                expect($result)->toBe(Icon::INFO);
            });

            it('ignores default icon when explicit icon provided', function () {
                $result = $this->resolver->resolve('✓', Icon::INFO);

                expect($result)->toBe('✓');
            });
        });
    });

    describe('integration with components', function () {

        it('affects Alert component globally', function () {
            // À tester dans les tests Alert avec la configuration
        });

        it('affects Badge component globally', function () {
            // À tester dans les tests Badge avec la configuration
        });

        it('affects Logger component globally', function () {
            // À tester dans les tests Logger avec la configuration
        });
    });
});
