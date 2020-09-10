#------------------------------------------------------------------------------
# Help function
#------------------------------------------------------------------------------

GREEN  := $(shell tput -Txterm setaf 2)
WHITE  := $(shell tput -Txterm setaf 7)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

TAB_LENGTH := 30

HELP_FUNC = \
    %help; \
    while(<>) { push @{$$help{$$2 // '_project'}}, [$$1, $$3] if /^([a-zA-Z\-]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
    	print "${YELLOW}usage:${RESET}\n"; \
    	print "  make [target]\n\n"; \
        for (sort keys %help) { \
        	print "${YELLOW}$$_:${RESET}\n"; \
    	for (@{$$help{$$_}}) { \
    		$$sep = " " x (${TAB_LENGTH} - length $$_->[0]); \
    		print "  ${GREEN}$$_->[0]${RESET}$$sep${WHITE}$$_->[1]${RESET}\n"; \
    	}; \
    	print "\n"; \
	}
