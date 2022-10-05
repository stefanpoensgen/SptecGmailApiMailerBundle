.DEFAULT_GOAL := help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help

ecs-fix: ## Run easy coding standard on php
	@php vendor/bin/ecs check --fix src bin tests
.PHONY: ecs-fix

phpstan: ## Run phpstan
	@composer dump-autoload
	@php vendor/bin/phpstan analyze --configuration phpstan.neon src tests
.PHONY: phpstan

phpunit: ## Run phpunit
	@composer dump-autoload
	@vendor/bin/phpunit $(test)
.PHONY: phpunit