.DEFAULT_GOAL := help

help:
	@printf "\033[33mUsage:\033[0m\n  make [target] [arg=\"val\"...]\n\n\033[33mTargets:\033[0m\n"
	@grep -E '^[-a-zA-Z0-9_\.\/]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2}'

.PHONY: install
install: vendor ## Installs all project dependencies

vendor: composer.json $(wildcard composer.lock)
	@composer install

.PHONY: ci
ci: phplint phpstan tester ## Runs complete CI suite

.PHONY: phplint
phplint: install
	@echo '> PHP linter ...'
	@php vendor/bin/parallel-lint Simplex/ tests/ example.php --colors
	@echo ''

.PHONY: phpstan
phpstan: install
	@echo '> PHPStan ...'
	@php .ci/phpstan analyse --configuration=.ci/phpstan.neon

.PHONY: tester
tester: install
	@echo '> tester ...'
	@php vendor/bin/tester tests/ -C --colors
