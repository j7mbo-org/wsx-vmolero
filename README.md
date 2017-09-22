# WSX

### Launching

Launch the docker container with:

`docker-compose up -d --build`

Access the container with:

`docker exec -i -t $(docker-compose ps | grep 'php' | awk '{print $1}') /bin/bash`

After running the docker container, visit:

`http://localhost:1337` 

### Stopping

To stop / remove the container, run:

`docker-compose stop` and/or `docker-compose rm`

To delete up all docker containers from the system, run:

`docker system prune -a`

--------

**Please stop / remove containers in-between tasks to avoid port conflicts.**