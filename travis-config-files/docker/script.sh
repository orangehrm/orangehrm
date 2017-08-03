if [[ ${TRAVIS_BRANCH} == develop ]];
then
    docker build --build-arg SEED=false -t $REPO:$TAGDEV .
fi

exit 0;
