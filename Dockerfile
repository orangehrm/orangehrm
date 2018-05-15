FROM ubuntu:14.04

MAINTAINER Orangehrm <samanthaj@orangehrm.com>

RUN apt-get update

# Install apache, PHP, and supplimentary programs. curl is for debugging the container.
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install apache2 mysql-server libapache2-mod-php5 php5-mysql php5-gd php-pear php-apc php5-curl curl supervisor

# Enable apache mods.
RUN a2enmod php5
RUN a2enmod rewrite

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid


ARG SEED=true
# Export port 80
EXPOSE 80

# add source to image
RUN mkdir -p var/www/site/orangehrm
COPY . var/www/site/orangehrm

#config mysql
RUN /usr/sbin/mysqld & \
    sleep 5s &&\
    echo "USE mysql;\nUPDATE user SET password=PASSWORD('root') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql


# Fix Permission
RUN cd var/www/site/orangehrm; bash fix_permissions.sh

#install application
RUN /usr/sbin/mysqld & \
    sleep 5s &&\
    cd var/www/site/orangehrm; php installer/cli_install.php 0

#Seed the database
RUN /usr/sbin/mysqld & \
    sleep 5s &&\
    cd var/www/site/orangehrm/travis-config-files; ./seeddb.sh


# Update the default apache site with the config we created.
ADD docker-build-files/apache-config.conf /etc/apache2/sites-enabled/000-default.conf

# Update the default apache ports with the config we created.
ADD docker-build-files/ports.conf /etc/apache2/ports.conf

# Copy Supervisor configuration
ADD docker-build-files/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Start apache/mysql
CMD /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf


