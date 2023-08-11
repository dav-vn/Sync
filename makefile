IMAGE := application-backend

serve: up
	composer run --timeout=0 serve

up: install build
	docker-compose up -d

install:
	composer install
	composer dump-autoload
	composer development-enable

build:
	docker build -t ${IMAGE} .