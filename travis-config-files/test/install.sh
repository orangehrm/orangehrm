wget https://getcomposer.org/composer-1.phar -O composer.phar
php composer.phar install -d symfony/lib
php composer.phar dump-autoload -o -d symfony/lib
php installer/cli_install.php 0
php devTools/general/create-test-db.php root

