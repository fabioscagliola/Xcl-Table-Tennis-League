build:
	docker compose -p xttl build --no-cache

start:
	docker compose -p xttl up -d --pull always

stop:
	docker compose -p xttl down

repeat: stop build start

