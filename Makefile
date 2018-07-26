export PATH := $(shell npm bin):$(PATH)

build:
	@docker run --rm --interactive --tty --volume $(PWD):/app composer install
	@docker-compose build
	@docker-compose up -d

seed:
	@docker-compose exec app php artisan migrate:refresh --seed

kill:
	@docker-compose kill
	@docker-compose rm --force

up:
	@docker-compose up -d

down:
	@docker-compose kill

test:
	@docker-compose exec app ./vendor/bin/phpunit
	@docker-compose exec app php artisan migrate:refresh --seed --quiet

clean:
	@rm -rf vendor node_modules

rebuild: kill clean build
