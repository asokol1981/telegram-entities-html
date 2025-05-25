
.PHONY: install
install:
	docker compose build --build-arg=$(shell id -u) || exit 0
	docker compose up -d || exit 0
	docker compose exec telegram-entities-html composer install || exit 0

.PHONY: build
build:
	docker compose build --tag=asokol1981/telegram-entities-html --build-arg=$(shell id -u) || exit 0

.PHONY: start
start:
	docker compose up -d || exit 0

.PHONY: stop
stop:
	docker compose down || exit 0

.PHONY: uninstall
uninstall:
	docker compose down --remove-orphans --volumes || exit 0
	docker rmi asokol1981/telegram-entities-html:latest || exit 0

.PHONY: exec
exec:
	docker compose exec -it telegram-entities-html $(filter-out $@,$(MAKECMDGOALS)) || exit 0

.PHONY: composer
composer:
	docker compose exec telegram-entities-html composer $(filter-out $@,$(MAKECMDGOALS)) || exit 0

.PHONY: test
test:
	docker compose exec telegram-entities-html php vendor/bin/phpunit $(filter-out $@,$(MAKECMDGOALS)) --coverage-text || exit 0

.PHONY: coverage
coverage:
	docker compose exec telegram-entities-html php vendor/bin/phpunit $(filter-out $@,$(MAKECMDGOALS)) --coverage-html coverage || exit 0

.PHONY: artisan
artisan:
	docker compose exec telegram-entities-html php artisan $(filter-out $@,$(MAKECMDGOALS))

.PHONY: php-cs-fixer
php-cs-fixer:
	docker compose exec telegram-entities-html vendor/bin/php-cs-fixer fix

# This empty rule prevents "make" from throwing an error
# when extra arguments (like "bash") are passed as targets.
%:
	@: