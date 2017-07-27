FROM php:7.0-apache

MAINTAINER Orangehrm <thulana@orangehrm.us.com>

RUN apt-get update

RUN DEBIAN_FRONTEND=noninteractive apt-get -y install mysql-server curl lynx-cur wget unzip supervisor php-apc

RUN docker-php-ext-install pdo pdo_mysql mysqli gd exif

# Export port 80
EXPOSE 80

RUN mkdir orangehrm
COPY . /var/www/html/orangehrm

#config mysql
RUN service mysql start & \

    sleep 5s &&\

    echo "USE mysql;\nUPDATE user SET password=PASSWORD('root') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql

# Fix Permission
RUN cd orangehrm; bash fix_permissions.sh

#install application
RUN service mysql restart & \

    sleep 10s &&\
 
    cd orangehrm; php installer/cli_install.php 0

# Update the default apache site with the config we created.
ADD docker-build-files/apache-config.conf /etc/apache2/sites-enabled/000-default.conf

# Update the default apache ports with the config we created.
ADD docker-build-files/ports.conf /etc/apache2/ports.conf

# Copy Supervisor configuration
ADD docker-build-files/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Start apache/mysql
CMD /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
