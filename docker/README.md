## OrangeHRM Open Source Development Environment

![Docker build](https://github.com/orangehrm/orangehrm-os-dev-environment/workflows/Docker%20build/badge.svg)

### Introduction
This project will facilitate inbuilt development environment for developers and testers 

#### Technology 
 - Docker
 - Docker compose 
 - Nginx
 - PHP 5.6, PHP 7.0, PHP 7.1, PHP 7.2, PHP 7.3, PHP 7.4, PHP 8.0, PHP 8.1, PHP 8.2

### Installation:
 1. Clone this project
 1. Copy the file __.env.dist__ to __.env__ and change (`LOCAL_SRC`) path to your project path
 1. update /etc/hosts file with following line
 __Example:__
 ```bash
 127.0.0.1	php56 php70 php71 php72 php73 php74 php80 php81 php82
 ```

 > Read the full guide [here](https://github.com/orangehrm/orangehrm-os-dev-environment/wiki/How-to-setup).
 
 > Note: All `docker-compose` commands should run inside this project root directory
 
#### How to up/down containers 
Up all containers
```bash
docker-compose up -d
```

Up only development containers 
```bash
docker-compose up -d php-8.1 mysql57
```
 
Down all container
```bash
docker-compose down
```

Stop all container
```bash
docker-compose stop
```
#### Env information 

| PHP Version  | Host | Start command |
| ------------- | ------------- | ------------- |
| PHP 8.2  | http://php82  | `$ bash ./scripts/php82` |
| PHP 8.1  | http://php81  | `$ bash ./scripts/php81` |
| PHP 8.0  | http://php80  | `$ bash ./scripts/php80` |
| PHP 7.4  | http://php74  | `$ bash ./scripts/php74` |
| PHP 7.3  | http://php73  | `$ bash ./scripts/php73` |
| PHP 7.2  | http://php72  | `$ bash ./scripts/php72` |
| PHP 7.1  | http://php71  | `$ bash ./scripts/php71` |
| PHP 7.0  | http://php70  | `$ bash ./scripts/php70` |
| PHP 5.6  | http://php56  | `$ bash ./scripts/php56` |

### Config & Database

| DB  | Host |User  | Password |
| --- | ---- |---- | ------- |
| MySQL 5.5  | mysql55  |root  | root  |
| MySQL 5.6  | mysql56  |root  | root  |
| MySQL 5.7  | mysql57  |root  | root  |
| MySQL 8.0  | mysql80  |root  | root  |
| MariaDB 5.5  | mariadb55  |root  | root  |
| MariaDB 10.0  | mariadb100  |root  | root  |
| MariaDB 10.1  | mariadb101  |root  | root  |
| MariaDB 10.2  | mariadb102  |root  | root  |
| MariaDB 10.3  | mariadb103  |root  | root  |
| MariaDB 10.4  | mariadb104  |root  | root  |
| MariaDB 10.5  | mariadb105  |root  | root  |
| MariaDB 10.6  | mariadb106  |root  | root  |
| MariaDB 10.7  | mariadb107  |root  | root  |
| MariaDB 10.8  | mariadb108  |root  | root  |
| MariaDB 10.9  | mariadb109  |root  | root  |
| MariaDB 10.10  | mariadb1010  |root  | root  |


To use the command line clients provided by the containers you can use the following commands:

```bash
# MySQL
docker-compose exec mysql55 mysql -u root -p"root"

# MariaDB
docker-compose exec mariadb55 mysql -u root -p"root"
```

#### How to browse OrangeHRM

- php 8.2 : http://php82:{PORT}/
- php 8.1 : http://php81:{PORT}/
- php 8.0 : http://php80:{PORT}/
- php 7.4 : http://php74:{PORT}/
- php 7.3 : http://php73:{PORT}/
- php 7.2 : http://php72:{PORT}/
- php 7.1 : http://php71:{PORT}/
- php 7.0 : http://php70:{PORT}/
- php 5.6 : http://php56:{PORT}/

> __Note:__ Here __PORT__ is either __NGINX_PORT__ or __NGINX_SSL_PORT__ which are defined in __.env__. When using __NGINX_SSL_PORT__ as the __PORT__ then URL should start with `https` instead of `http`.

#### Run commands in a running container
__Execute in PHP 7.4:__
```bash
docker-compose exec php-7.4 bash
# OR
docker exec -it os_dev_php74 bash
```

__Execute in PHP 7.3:__
```bash
docker exec -it os_dev_php73 bash
```

__Execute in PHP 7.2:__
```bash
docker exec -it os_dev_php72 bash
```

__Execute in PHP 7.1:__
```bash
docker exec -it os_dev_php71 bash
```

__Execute in PHP 7.0:__
```bash
docker exec -it os_dev_php70 bash
```

#### Build Images
```bash
# Build all images
docker-compose -f docker-compose.yml -f docker-compose-build.yml build
# OR only specific
docker-compose -f docker-compose.yml -f docker-compose-build.yml build nginx
```
