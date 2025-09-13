FROM ubuntu:22.04

# Prevent interactive prompts
ENV DEBIAN_FRONTEND=noninteractive
ENV APP_HOME=/deeptune/app

WORKDIR $APP_HOME

# --- Install prerequisites ---
RUN apt-get update && apt-get install -y software-properties-common curl gnupg lsb-release

# Add PostgreSQL 16 repo
RUN echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && curl -fsSL https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -

# Install Apache, PHP, PostgreSQL, Supervisor, Node, Composer
RUN apt-get update && apt-get install -y \
    apache2 postgresql-16 supervisor \
    php8.1 php8.1-cli php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-bcmath php8.1-gd php8.1-intl \
    libapache2-mod-php8.1 \
    nodejs npm composer git unzip \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite headers

# Copy supervisord config and entrypoint
COPY .deeptune/supervisord.conf /deeptune/supervisord.conf
COPY .deeptune/entrypoint.sh /deeptune/entrypoint.sh
RUN chmod +x /deeptune/entrypoint.sh

# Copy OrangeHRM source code
COPY . $APP_HOME

# Install PHP dependencies (if composer.json exists)
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Build frontend if package.json exists
RUN if [ -f package.json ]; then npm install && npm run build; fi

# Setup PostgreSQL directories
RUN mkdir -p /var/lib/postgresql/16/main /var/run/postgresql/16-main.pg_stat_tmp \
    && chown -R postgres:postgres /var/lib/postgresql/16/main /var/run/postgresql/16-main.pg_stat_tmp

# Apache virtual host for OrangeHRM
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /deeptune/app/web\n\
    <Directory /deeptune/app/web>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Expose ports: Apache (80) + Postgres (5432)
EXPOSE 80 5432

# Entrypoint -> supervisord
CMD ["/deeptune/entrypoint.sh"]

