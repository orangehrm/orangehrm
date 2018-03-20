echo "Run php in-built server - background process"
nohup bash -c "php -S 127.0.0.1:8888 2>&1 -t symfony/web  >/dev/null 2>&1 &"
sleep 4
mkdir -p build/logs