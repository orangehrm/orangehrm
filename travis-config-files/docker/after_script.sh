if [[ ${TRAVIS_EVENT_TYPE} == pull_request ]];
then
    exit 0;
else

    if [[ ${TRAVIS_BRANCH} == develop ]];
    then
        docker login -u="$DOCKER_USERNAME" -p="$DOCKER_PASSWORD"
        docker push $REPO:$TAGDEV
    else
        docker login -u="$DOCKER_USERNAME" -p="$DOCKER_PASSWORD"
        docker push $REPO:$TAGMASTER
    fi
    exit 0;
fi

