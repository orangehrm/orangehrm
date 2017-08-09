
if [[ ${TRAVIS_EVENT_TYPE} == pull_request ]];
then
    exit 0;
else
    if [[ ${TRAVIS_BRANCH} == develop ]];
    then
        docker build --build-arg SEED=false -t $REPO:$TAGDEV .

    else
        docker build --build-arg SEED=false -t $REPO:$TAGMASTER .

    fi
    exit 0;
fi

