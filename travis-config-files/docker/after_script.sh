if [[ ${TRAVIS_BRANCH} == develop ]];
then
    docker login -u="$DOCKER_USERNAME" -p="$DOCKER_PASSWORD"
    docker push $REPO:$TAGDEV
fi

exit 0;
