FROM ubuntu:22.04

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

# Enable Apache rewrite and headers modules
RUN a2enmod rewrite headers

# Copy supervisord config and entrypoint
COPY .deeptune/supervisord.conf /deeptune/supervisord.conf
COPY .deeptune/entrypoint.sh /deeptune/entrypoint.sh
RUN chmod +x /deeptune/entrypoint.sh

# Copy OrangeHRM source code
COPY . $APP_HOME

# Install PHP dependencies if composer.json exists
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

# Build frontend if package.json exists
RUN if [ -f package.json ]; then npm install && npm run build; fi

# ---- PostgreSQL cluster initialization ----
USER postgres
RUN if [ ! -d "/var/lib/postgresql/16/main" ]; then \
      /usr/lib/postgresql/16/bin/initdb -D /var/lib/postgresql/16/main && \
      echo "host all all 0.0.0.0/0 trust" >> /var/lib/postgresql/16/main/pg_hba.conf && \
      echo "listen_addresses='*'" >> /var/lib/postgresql/16/main/postgresql.conf; \
    fi
# Set default password for 'postgres' user
RUN /usr/lib/postgresql/16/bin/pg_ctl -D /var/lib/postgresql/16/main -o "-c listen_addresses='localhost'" -w start && \
    psql --command="ALTER USER postgres PASSWORD 'postgres';" && \
    /usr/lib/postgresql/16/bin/pg_ctl -D /var/lib/postgresql/16/main stop

USER root

# Ensure log directory exists for supervisor
RUN mkdir -p /var/log/postgresql && touch /var/log/postgresql/postgresql-16-main.log && chown -R postgres:postgres /var/log/postgresql

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

EXPOSE 80 5432

CMD ["/deeptune/entrypoint.sh"]

