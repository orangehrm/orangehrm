FROM php:7.3.15-apache

LABEL maintainer="samanthaj@orangehrm.com"

# Install PHP extensions
# https://hub.docker.com/r/mlocati/php-extension-installer/tags
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
	ioncube_loader \
	gd \
	opcache \
	mysqli \
	pdo_mysql \
	zip \
	ldap \
	&& rm /usr/local/bin/install-php-extensions

RUN set -ex \
	&& apt-get update \
	&& apt-get install -y --no-install-recommends \
	unzip \
	&& rm -rf /var/cache/apt/archives \
	&& rm -rf /var/lib/apt/lists/*

ARG OHRM_VERSION=4.8
ARG OHRM_MD5=55c9a4657334ad6051f009a8a41fad02
RUN cd .. \
	&& rm -r html \
	&& curl -fSL -o orangehrm.zip "https://sourceforge.net/projects/orangehrm/files/stable/${OHRM_VERSION}/orangehrm-${OHRM_VERSION}.zip" \
	&& echo "${OHRM_MD5} orangehrm.zip" | md5sum -c - \
	&& unzip -q orangehrm.zip "orangehrm-${OHRM_VERSION}/*" \
	&& mv orangehrm-${OHRM_VERSION} html \
	&& rm -f orangehrm.zip

ARG UID=1001
RUN groupadd -g ${UID} -r orangehrm \
	&& useradd -u ${UID} -r -g orangehrm -M orangehrm \
	&& cd .. \
	&& chown ${UID}:${UID} html \
	&& chown -R ${UID}:${UID} html/symfony/cache html/symfony/log \
	&& chmod -R 775 html/symfony/cache html/symfony/log

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

USER 1001

VOLUME ["/var/www/html"]
