php81:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php81 /bin/sh

stan81:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php81 composer stan

php82:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php82 /bin/sh

stan82:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php82 composer stan

php83:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php83 /bin/sh

stan83:
	@docker compose exec -e XDEBUG_MODE=off -w /opt/project php83 composer stan

