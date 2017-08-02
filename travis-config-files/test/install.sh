composer install -d symfony/lib
composer dump-autoload -o -d symfony/lib
wget -c -nc --retry-connrefused --tries=0 https://github.com/satooshi/php-coveralls/releases/download/v1.0.1/coveralls.phar
chmod +x coveralls.phar
php coveralls.phar --version
php installer/cli_install.php 0
php devTools/general/create-test-db.php root

