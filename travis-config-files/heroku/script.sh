if [[ ${TRAVIS_BRANCH} == develop ]];
then
    heroku container:push web --app $HEROKU_APP_NAME
fi

exit 0;
