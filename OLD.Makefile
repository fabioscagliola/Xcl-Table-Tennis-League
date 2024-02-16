build:
	docker compose -p singleq build --no-cache

start:
	docker compose -p singleq up -d --pull always

stop:
	docker compose -p singleq down

repeat: stop build start

