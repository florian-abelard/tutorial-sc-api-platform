#------------------------------------------------------------------------------
# Docker Makefile
#------------------------------------------------------------------------------

build: ##@docker build containers
	docker-compose -f ${DOCKER_COMPOSE_FILE} build

up: .env ##@docker build and start containers
	docker-compose -f ${DOCKER_COMPOSE_FILE} up -d

down: ##@docker stop and remove containers and volumes
	docker-compose -f ${DOCKER_COMPOSE_FILE} down --volumes

rebuild: build up ##@docker rebuild and start containers

logs: ##@docker displays containers log
	docker-compose logs -f -t --tail="5"

#------------------------------------------------------------------------------

clean-docker: down ##@docker clean docker containers
	docker container ls -a | grep "${APP_NAME}" | awk '{print $1}' | xargs --no-run-if-empty docker container rm
	docker image rm $(docker images -a -q)

#------------------------------------------------------------------------------

.PHONY: up build down rebuild connect clean-docker
