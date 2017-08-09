if [[ ${TRAVIS_EVENT_TYPE} == pull_request ]];
then
    exit 0;
else
    sudo chmod 777 -R symfony/cache
    sudo chmod 777 -R symfony/log
    composer install -d symfony/lib
    composer dump-autoload -o -d symfony/lib
    wget -qO- https://toolbelt.heroku.com/install-ubuntu.sh | sh
    heroku plugins:install heroku-container-registry
    docker login -e _ -u _ --password=$HEROKU_API_KEY registry.heroku.com
    #echo "ENV TRAVIS_JOB_NO ${TRAVIS_JOB_NUMBER}" >> Dockerfile
    #echo "ENV TRAVIS_BUILD_NO ${TRAVIS_BUILD_NUMBER}" >> Dockerfile
    exit 0;
fi