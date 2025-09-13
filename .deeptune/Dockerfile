FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV APP_HOME=/deeptune/app

WORKDIR $APP_HOME

# --- Install prerequisites ---
RUN apt-get update && apt-get install -y software-properties-common curl gnupg lsb-release

# Install Apache, PHP, MySQL, Supervisor, Node, Composer
RUN apt-get update && apt-get install -y \
    apache2 mysql-server supervisor \
    php8.1 php8.1-cli php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-bcmath php8.1-gd php8.1-intl \
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

EXPOSE 80 3306

CMD ["/deeptune/entrypoint.sh"]
