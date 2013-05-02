chmod go+w installer symfony/apps/orangehrm/config
chmod -R go+w lib/confs symfony/log symfony/cache lib/logs symfony/config upgrader/cache upgrader/log
if [ -f installer/log.txt ]
then
    chmod go+w installer/log.txt
fi

