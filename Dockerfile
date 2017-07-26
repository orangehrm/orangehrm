FROM ubuntu:14.04
MAINTAINER Orangehrm <samanthaj@orangehrm.com>

RUN apt-get update
RUN apt-get -y upgrade
RUN apt-get -y install software-properties-common
RUN apt-get install -y language-pack-en-base 


# Install apache, PHP, and supplimentary programs. curl and lynx-cur are for debugging the container.
RUN DEBIAN_FRONTEND=noninteractive LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php && apt-get -y install apache2 mysql-server libapache2-mod-php7.0 php7.0-mysql php7.0-gd php-pear php-apc php7.0-curl curl lynx-cur wget unzip supervisor ssh && \
	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Enable apache mods.
RUN a2enmod php5
RUN a2enmod rewrite

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Export port 80
EXPOSE 80

# add source to image

RUN mkdir -p var/www/site/orangehrm
COPY . var/www/site/orangehrm
#RUN wget -c http://downloads.sourceforge.net/project/orangehrm/stable/3.3.2/orangehrm-3.3.2.zip -O ~/orangehrm-3.3.2.zip &&\
 #   unzip -o ~/orangehrm-3.3.2.zip -d /var/www/site && \
  #  rm ~/orangehrm-3.3.2.zip

#config mysql
RUN /usr/sbin/mysqld & \

    sleep 10s &&\

    echo "USE mysql;\nUPDATE user SET password=PASSWORD('root') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql


# Fix Permission
RUN cd /var/www/site/orangehrm; bash fix_permissions.sh

#init project
RUN cd /var/www/site/orangehrm; composer self-update
RUN cd /var/www/site/orangehrm; composer install -d symfony/lib
RUN cd /var/www/site/orangehrm; composer dump-autoload -o -d symfony/lib

#install application
RUN /usr/sbin/mysqld & \

    sleep 10s &&\
 
    cd /var/www/site/orangehrm; php installer/cli_install.php 0

# Update the default apache site with the config we created.
ADD docker-build-files/apache-config.conf /etc/apache2/sites-enabled/000-default.conf

# Update the default apache ports with the config we created.
ADD docker-build-files/ports.conf /etc/apache2/ports.conf

# Copy Supervisor configuration
ADD docker-build-files/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Start apache/mysql
CMD /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf


