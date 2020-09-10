#------------------------------------------------------------------------------
# Database Makefile
# 	- with doctrine
#------------------------------------------------------------------------------

database-admin-exec = docker-compose -f ${DOCKER_COMPOSE_FILE} exec -T --user root db ${1}
database-doctrine-exec = docker-compose -f ${DOCKER_COMPOSE_FILE} exec -T --user ${USER_ID} php ${1}

#------------------------------------------------------------------------------

db-init: db-create db-migrate ##@database create and populate database

db-create: db-drop ##@database create the database
	$(call database-doctrine-exec, php bin/console doctrine:database:create)

db-drop: ##@database drop the database
	$(call database-doctrine-exec, php bin/console doctrine:database:drop --if-exists --force)

db-migrate: ##@database run the database migrations 
	$(call database-doctrine-exec, php bin/console doctrine:migrations:migrate)

db-create-migration: ##@database create a new migration file
	$(call database-doctrine-exec, php bin/console doctrine:migrations:diff)

db-schema-update: 
	$(call database-doctrine-exec, php bin/console doctrine:schema:update --force)

#------------------------------------------------------------------------------

clean-db: db-drop ##@database clean database

#------------------------------------------------------------------------------

.PHONY: db-init db-create db-drop db-migrate db-create-migration db-schema-update clean-db
