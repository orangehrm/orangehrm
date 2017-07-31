sudo chmod 777 -R symfony/cache
sudo chmod 777 -R symfony/log
composer install -d symfony/lib
composer dump-autoload -o -d symfony/lib
