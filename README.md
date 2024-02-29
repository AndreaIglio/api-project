API-PROJECT
========================

![Symfony 6.4](https://img.shields.io/badge/Symfony-6.4-purple.svg?style=flat-square&logo=symfony)
![Php 8.3.3](https://img.shields.io/badge/Php-8.3.3-blue.svg?style=flat-square&logo=php)
![MySql 8.0](https://img.shields.io/badge/MySql-8.0-red.svg?style=flat-square&logo=mysql)
![Nginx](https://img.shields.io/badge/Nginx-green.svg?style=flat-square&logo=nginx)
![Docker](https://img.shields.io/badge/Docker-yellow.svg?style=flat-square&logo=yellow)

## Preparation of development environment
The application runs inside a docker container. Prepare the environment in this way:

#### Requirements
- Docker
- Docker compose

#### First start
The environment is containerized with Docker, follow these steps to start the project:

```
git clone https://github.com/AndreaIglio/api-project.git
docker-compose up -d
docker-compose start
composer install

```

#### Tools
The project use some development tools to guarantee code quality and standard, you can run them using the composer scripts:

- composer phpstan (static code analyse)
- composer phpunit (test)
- composer ecs (fix code style)