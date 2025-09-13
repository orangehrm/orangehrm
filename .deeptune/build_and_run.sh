 #!/bin/bash
 set -e
# Script to build the Docker image for local development
 # Set environment variables
 DEEPTUNE_APP_ENV="orangehrm"
 DOCKER_IMAGE_NAME="deeptune-${DEEPTUNE_APP_ENV}-base:latest"
 DOCKERFILE_PATH=".deeptune/Dockerfile"
# Build the Docker image
 echo "Building Docker image..."
 docker build -t $DOCKER_IMAGE_NAME -f $DOCKERFILE_PATH . || { echo 
"Build failed"; exit 1; }
 echo "Build successful"
 # Run the Docker container
 echo "Running Docker container..."
 # Make sure to update these ports to whatever your app needs exposed
 docker run -p 3000:3000 -p 5432:5432 -p 80:80 $DOCKER_IMAGE_NAME
 echo "Container running"


