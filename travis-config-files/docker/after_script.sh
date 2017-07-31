docker login -u="$DOCKER_USERNAME" -p="$DOCKER_PASSWORD"
docker push $REPO:$TAG  
