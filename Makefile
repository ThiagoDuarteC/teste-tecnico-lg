setup:
	docker-compose up -d --build
	docker-compose exec app composer update
	docker-compose exec app cp .env.example .env
	docker-compose exec app php artisan key:generate
	docker-compose exec app composer require laravel/ui:^2.0
	docker-compose exec app php artisan ui bootstrap --auth
	docker-compose exec app npm install
	docker-compose exec app npm install vue-template-compiler --save-dev
	docker-compose exec app npm run dev

up:
	docker-compose up -d

down:
	docker-compose down