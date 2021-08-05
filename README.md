# coms_test
Test task for Coms

REST API for news entity, including:
* GET /news
* GET /news/{id}
* POST /news
* PUT /news/{id} (including partial replace - like PATCH)
* DELETE /news/{id}

Added docker support, docker-compose example for fpm+mysql, no webserver.

RabbitMQ support is not finished because of lack of experience.
