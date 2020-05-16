FROM php:7.3.15-apache-buster

LABEL maintainer="samanthaj@orangehrm.com"

ENV OHRM_VERSION 4.4
ENV OHRM_MD5 a4ee1052977a28c1262ea27b073c22f3
ENV IONCUBE_MD5 431155d4a1b4d1b6a651c9c65476ef58

RUN set -ex; \
    curl -fSL -o ioncube.tar.gz "https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz"; \
    echo "${IONCUBE_MD5} ioncube.tar.gz" | md5sum -c -; \
    tar -xvvzf ioncube.tar.gz; \
    mv ioncube/ioncube_loader_lin_7.3.so `php-config --extension-dir`; \
    rm -rf ioncube.tar.gz ioncube; \
    docker-php-ext-enable ioncube_loader_lin_7.3

RUN set -ex; \
	savedAptMark="$(apt-mark showmanual)"; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libfreetype6-dev \
		libjpeg-dev \
		libpng-dev \
		libzip-dev \
		libldap2-dev \
		unzip \
	; \
	\
	cd .. && rm -r html; \
	curl -fSL -o orangehrm.zip "https://sourceforge.net/projects/orangehrm/files/stable/${OHRM_VERSION}/orangehrm-${OHRM_VERSION}.zip"; \
	echo "${OHRM_MD5} orangehrm.zip" | md5sum -c -; \
	unzip -q orangehrm.zip "orangehrm-${OHRM_VERSION}/*"; \
	mv orangehrm-$OHRM_VERSION html; \
	rm -rf orangehrm.zip; \
	chown www-data:www-data html; \
	chown -R www-data:www-data html/symfony/cache html/symfony/log; \
	chmod -R 775 html/symfony/cache html/symfony/log; \
	\
	docker-php-ext-configure gd \
		--with-freetype-dir=/usr \
		--with-png-dir=/usr \
		--with-jpeg-dir=/usr \
	; \
	docker-php-ext-configure ldap \
	    --with-libdir=lib/x86_64-linux-gnu/ \
	; \
	\
	docker-php-ext-install -j "$(nproc)" \
		gd \
		opcache \
		mysqli \
		pdo_mysql \
		zip \
		ldap \
	; \
	\
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	rm -rf /var/cache/apt/archives; \
	rm -rf /var/lib/apt/lists/*

RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=60'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini; \
	\
	if command -v a2enmod; then \
		a2enmod rewrite; \
	fi;
