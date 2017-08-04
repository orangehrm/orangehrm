if [[ ${TRAVIS_BRANCH} == develop ]];
then
    heroku container:push web --app $DEV_HEROKU_APP_NAME

else
    heroku container:push web --app $MASTER_HEROKU_APP_NAME

fi

exit 0;
