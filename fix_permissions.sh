chmod go+w installer symfony/apps/orangehrm/config
chmod -R go+w lib/confs symfony/log symfony/cache lib/logs symfony/config upgrader/cache upgrader/log
if [ -f symfony/log/installer.log ]
then
    chmod go+w symfony/log/installer.log
fi

