 #!/bin/bash
set -e

# Environment setup
DEEPTUNE_APP_ENV="orangehrm"
DOCKER_IMAGE_NAME="deeptune-${DEEPTUNE_APP_ENV}-base:latest"
DOCKERFILE_PATH="Dockerfile"

echo "Building Docker image..."
 docker build -t $DOCKER_IMAGE_NAME -f $DOCKERFILE_PATH ..|| { echo 
"Build failed"; exit 1; }
echo "Build successful"

echo "Running Docker container..."
docker run -d \
  --name ${DEEPTUNE_APP_ENV}_container \
  -p 80:80 \
  -p 3306:3306 \
  $DOCKER_IMAGE_NAME

echo "Container is up and running"



