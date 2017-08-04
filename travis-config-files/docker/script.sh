if [[ ${TRAVIS_BRANCH} == develop ]];
then
    docker build --build-arg SEED=false -t $REPO:$TAGDEV .

else
    docker build --build-arg SEED=false -t $REPO:$TAGMASTER .

fi

exit 0;
