php81:
	@docker compose exec -e XDEBUG_MODE=off -e COMPOSER_ROOT_VERSION=6 -w /opt/project hibp-php-php81 /bin/sh

stan81:
	@docker compose exec -e XDEBUG_MODE=off -e COMPOSER_ROOT_VERSION=6 -w /opt/project hibp-php-php81 composer stan

start-php81:
	@docker compose up hibp-php-php81 -d

php82:
	@docker compose exec -e XDEBUG_MODE=off -e COMPOSER_ROOT_VERSION=6 -w /opt/project hibp-php-php82 /bin/sh

stan82:
	@docker compose exec -e XDEBUG_MODE=off -e COMPOSER_ROOT_VERSION=6 -w /opt/project hibp-php-php82 composer stan

start-php82:
	@docker compose up hibp-php-php82 -d

php83:
	@docker compose exec -e XDEBUG_MODE=off -e COMPOSER_ROOT_VERSION=6 -w /opt/project hibp-php-php83 /bin/bash

stan83:
	@docker compose exec -e XDEBUG_MODE=off -e COMPOSER_ROOT_VERSION=6 -w /opt/project hibp-php-php83 composer stan

start-php83:
	@docker compose up hibp-php-php83 -d

