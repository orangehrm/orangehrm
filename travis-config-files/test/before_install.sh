uname -a
phpunit --version
mysqladmin -uroot status
composer require satooshi/php-coveralls:dev-master
composer self-update
sudo chmod 777 -R symfony/cache
sudo chmod 777 -R symfony/log
echo "USE mysql;\nUPDATE user SET password=PASSWORD('root') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql -u root
