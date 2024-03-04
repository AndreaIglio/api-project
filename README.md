API-PROJECT
========================

![Symfony 6.4](https://img.shields.io/badge/Symfony-6.4-purple.svg?style=flat-square&logo=symfony)
![Php 8.2.16](https://img.shields.io/badge/Php-8.2.16-blue.svg?style=flat-square&logo=php)
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
docker-compose exec -it php zsh
```
After the containers are up and the dependencies are installed, you need to create the PEM keys for JWT authentication.
Run the following commands:
```
bin/console lexik:jwt:generate-keypair
```
Then you should see the public.pem and private.pem in the %kernel.project_dir%/config/jwt/

#### Database & Migration
To create database for dev and test environment and to run migrations run the following commands inside the container:
(An admin user will be automatically generated)
```
bin/console d:d:c
bin/console d:d:c --env=test
bin/console d:m:m
bin/console d:m:m --env=test
```

#### API Documentation

* [Authentication](documentation/authentication/authentication.md)
* [Customer](documentation/api/customer/customer.md)
* [Manager](documentation/api/manager/manager.md)
* [MultimediaResource](documentation/api/multimedia_resource/multimedia_resource.md)

#### Tools
The project use some development tools to guarantee code quality and standard, you can run them using the composer scripts:

- composer phpstan (static code analyse)
- composer phpunit (test)
- composer ecs (fix code style)

- composer test to run all three tools