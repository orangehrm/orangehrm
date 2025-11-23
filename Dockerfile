FROM php:8.3-apache-bookworm


RUN set -ex; \
	savedAptMark="$(apt-mark showmanual)"; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libfreetype6-dev \
		libjpeg-dev \
		libpng-dev \
		libzip-dev \
		libldap2-dev \
		libicu-dev \
		unzip \
		git \
		curl \
	; \
	\

	\
	# Configure and install PHP extensions
	docker-php-ext-configure gd --with-freetype --with-jpeg; \
	docker-php-ext-configure ldap \
	    --with-libdir=lib/$(uname -m)-linux-gnu/ \
	; \
	\
	docker-php-ext-install -j "$(nproc)" \
		gd \
		opcache \
		intl \
		pdo_mysql \
		zip \
		ldap \
	; \
	\
	# Clean up unnecessary packages but keep Node.js and npm
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark nodejs; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { so = $(NF-1); if (index(so, "/usr/local/") == 1) { next }; gsub("^/(usr/)?", "", so); print so }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	rm -rf /var/cache/apt/archives; \
	rm -rf /var/lib/apt/lists/*

# Configure PHP OPcache
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

# Update PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy local source code to container (uses .dockerignore to exclude unnecessary files)
COPY --chown=www-data:www-data . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Install Composer for PHP dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies with Composer
RUN cd src && \
	echo "Installing PHP Composer dependencies..." && \
	composer install --no-dev --optimize-autoloader && \
	echo "Composer dependencies installed!" && \
	cd ..

# Build Vue.js frontends with custom application form fields
# Set CI=false to prevent build from failing on linting warnings
ENV CI=false

# Build main application frontend
RUN cd src/client && \
	echo "Installing main app frontend dependencies..." && \
	yarn install --frozen-lockfile && \
	echo "Building main app Vue.js frontend with custom application form..." && \
	yarn build --skip-plugins @vue/cli-plugin-eslint && \
	echo "Main app frontend build completed!" && \
	cd ../..

# Build installer frontend
RUN cd installer/client && \
	echo "Installing installer frontend dependencies..." && \
	yarn install --frozen-lockfile && \
	echo "Building installer Vue.js frontend..." && \
	yarn build --skip-plugins @vue/cli-plugin-eslint && \
	echo "Installer frontend build completed!" && \
	cd ../..

# Set proper permissions for writable directories
RUN chown -R www-data:www-data /var/www/html && \
	chmod -R 775 /var/www/html/src/cache /var/www/html/src/log /var/www/html/src/config

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
