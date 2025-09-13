#!/bin/bash
 set -e
 # Specify environment variables
 DEEPTUNE_APP_ENV="orangehrm"
 ECR_REPOSITORY_NAME="deeptune/app-${DEEPTUNE_APP_ENV}-base"
 AWS_REGION=${AWS_REGION:-"us-east-1"}
 # Verify AWS credentials
 echo "🔍Verifying AWS credentials..."
 aws sts get-caller-identity &>/dev/null || { echo "❌Error: Invalid AWS credentials"; exit 1; }
 # Verify ECR repository exists
 echo "🔍Verifying ECR repository exists..."
 aws ecr describe-repositories --repository-names "${ECR_REPOSITORY_NAME}" &>/dev/null || { echo "❌Error: ECR repository ${ECR_REPOSITORY_NAME} not found, have you made the repo in ECR?"; exit 1; }
 # Get AWS account ID
 AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
 # Log in to ECR
 echo "🔑Logging in to ECR..."
 aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com
 
 # Find the highest numeric tag in the ECR repository
 # This command queries ECR for all image tags, filters for numeric tags, and finds the maximum
echo "🔍Finding current highest version tag in ECR repository: ${ECR_REPOSITORY_NAME}"
 LATEST_TAG=$(aws ecr describe-images \--repository-name "${ECR_REPOSITORY_NAME}" \--query 'sort_by(imageDetails,& imagePushedAt)[*].imageTags[*]' \--output json | \
 jq -r 'flatten | map(select(test("^[0-9]+$"))) | map(tonumber) | max // 0')
 # Increment the highest tag to get the new version
 NEW_TAG=$((LATEST_TAG + 1))
 
 # Define ECR image URIs for both the new version and latest
 ECR_IMAGE_URI_BASE="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPOSITORY_NAME}"
 ECR_IMAGE_URI_VERSIONED="${ECR_IMAGE_URI_BASE}:${NEW_TAG}"
 ECR_IMAGE_URI_LATEST="${ECR_IMAGE_URI_BASE}:latest"
 echo "📊Current highest tag: ${LATEST_TAG}"
 echo "🏷New tag will be: ${NEW_TAG}"
 echo "🔨Building and pushing multi-architecture Docker image for ${DEEPTUNE_APP_ENV}..."
 DOCKER_BUILDKIT=1 docker buildx create --use --name multi-arch-builder || true
 DOCKER_BUILDKIT=1 docker buildx build \--platform linux/amd64,linux/arm64 \--compress \-t ${ECR_IMAGE_URI_VERSIONED} \-t ${ECR_IMAGE_URI_LATEST} \-f .deeptune/Dockerfile \--push . || 
 { echo "❌Build failed"; exit 1; }
 echo "Successfully pushed image to ${ECR_IMAGE_URI_VERSIONED} and ${ECR_IMAGE_URI_LATEST}"
