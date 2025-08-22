# Caminhos e variáveis
EXEC_PHP = docker exec exemplo-fiap-php
COMPOSE = docker compose -f docker/docker-compose.yml

.PHONY: check-docker
.PHONY: artisan

# Docker
up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

check-docker:
	@count=$$(docker ps -q | wc -l); \
	if [ "$$count" -eq 0 ]; then \
		echo "⚠️  Nenhum container está rodando."; \
	else \
		echo "✅ Existem $$count containers em execução."; \
	fi

restart: down up

# Artisan
artisan:
	$(EXEC_PHP) php artisan $(filter-out $@,$(MAKECMDGOALS))

# Composer
test:
	$(EXEC_PHP) ./vendor/bin/phpunit

cs:
	$(EXEC_PHP) ./vendor/bin/phpcs --standard=phpcs.xml

cbf:
	$(EXEC_PHP) ./vendor/bin/phpcbf --standard=phpcs.xml

export_tests:
	$(EXEC_PHP) XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-clover=phpunit-coverage-result.xml --log-junit=phpunit-execution-result.xml --coverage-html .phpunit_report

stan:
	$(EXEC_PHP) ./vendor/bin/phpstan analyse -c phpstan.neon > phpstan.log

md:
	$(EXEC_PHP) ./vendor/bin/phpmd src html ruleset.xml > phpmd.html
