IMAGE := application-backend

serve: up
	composer run --timeout=0 serve

up: install build
	docker-compose up -d

install:
	composer install --ignore-platform-reqs
	composer dump-autoload
	composer development-enable

build:
	docker build -t ${IMAGE} .