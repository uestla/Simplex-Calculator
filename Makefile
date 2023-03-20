.DEFAULT_GOAL := help

help:
	@printf "\033[33mUsage:\033[0m\n  make [target] [arg=\"val\"...]\n\n\033[33mTargets:\033[0m\n"
	@grep -E '^[-a-zA-Z0-9_\.\/]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2}'

.PHONY: install
install: install-composer vendor ## Installs all project dependencies

.PHONY: install-composer
install-composer:
	@[ -f bin/composer ] \
		|| (echo '> Installing composer...' \
		&& php -r 'copy("https://getcomposer.org/installer", "composer-setup.php");' \
		&& php -r 'if (hash_file("sha384", "composer-setup.php") === file_get_contents("https://composer.github.io/installer.sig")) { echo "Installer verified"; } else { echo "Installer corrupt"; unlink("composer-setup.php"); } echo PHP_EOL;' \
		&& mkdir -p bin \
		&& php composer-setup.php --install-dir=bin --filename=composer --2 \
		&& rm composer-setup.php)

vendor: composer.json $(wildcard composer.lock)
	@echo '> composer install...' \
		&& bin/composer install \
		&& echo ''

.PHONY: ci
ci: phplint phpstan tester ## Runs complete CI suite

.PHONY: phplint
phplint: install
	@echo '> PHP linter ...'
	@php vendor/bin/parallel-lint src/ tests/ --colors --no-progress
	@echo ''

.PHONY: phpstan
phpstan: install
	@echo '> PHPStan ...'
	@php vendor/bin/phpstan analyse --no-progress

.PHONY: tester
tester: install
	@echo '> Nette Tester ...'
	@php vendor/bin/tester tests/ -C --colors --coverage tests/coverage.html --coverage-src src/
