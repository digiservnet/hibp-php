php81:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php81 /bin/sh

stan81:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php81 composer stan

php82:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php82 /bin/bash

stan82:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php82 composer stan

php83:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php83 /bin/bash

stan83:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php83 composer stan

php84:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php84 /bin/bash

stan84:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php84 composer stan

