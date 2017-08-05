.PHONY: build lint

CUID = $(shell id -u)
CGID = $(shell id -g)

IMAGE_FRONT := skilldlabs/frontend:zen
NODE_ENV ?= testing
front = docker run --rm -u $(CUID):$(CGID) -v $(shell pwd)/STARTERKIT:/work -e NODE_ENV=$(NODE_ENV) $(IMAGE_FRONT) ${1}

all: | build

build:
	@echo "Running build tasks..."
	docker pull $(IMAGE_FRONT)
	$(call front)

lint:
	@echo "Running linters..."
	$(call front, gulp lint)
