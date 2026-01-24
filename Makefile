# Define Docker Compose file
COMPOSE_FILE = docker/docker-compose.yaml

# Build Docker images
build:
	docker compose -f $(COMPOSE_FILE) build

# Start Docker containers in detached mode
up:
	docker compose -f $(COMPOSE_FILE) up -d

# Stop Docker containers
down:
	docker compose -f $(COMPOSE_FILE) down

# List running Docker containers
ps:
	docker compose -f $(COMPOSE_FILE) ps

# Show Docker container logs
logs:
	docker compose -f $(COMPOSE_FILE) logs -f

# Execute a shell command in the app container
shell:
	docker compose -f $(COMPOSE_FILE) exec app bash

.PHONY: build up down ps logs shell
