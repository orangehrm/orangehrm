# 📦 Dockerize Guide -- README

This project has been Dockerized to run as a **monolithic container**.
All components (frontend, backend, services, and databases) are included
in a **single Dockerfile** for compatibility with Deeptune's AI training
clusters.

------------------------------------------------------------------------

## ⚙️ Project Structure

    .deeptune/
     ├── Dockerfile           # Monolithic Docker build
     ├── supervisord.conf     # Supervisor config to run all services
     ├── entrypoint.sh        # Entry script for container startup
     ├── build_and_run.sh     # Local build and run helper
     ├── deploy_to_ecr.sh     # Script to push image to AWS ECR
     └── README.md            # Documentation (this file)

------------------------------------------------------------------------

## 🏗 Building & Running Locally

### Build

``` bash
bash .deeptune/build_and_run.sh
```

This script will: - Build the Docker image\
- Start a container exposing required ports (update if your app uses
different ones)

### Run manually

``` bash
docker build -t deeptune-yourappname-base:latest -f .deeptune/Dockerfile .
docker rm deeptune-yourappname-monolithic -f
docker run --name deeptune-yourappname-monolithic -d   -p 3000:3000 -p 5432:5432 -p 8080:8080   deeptune-yourappname-base:latest
docker logs -f deeptune-yourappname-monolithic
```

------------------------------------------------------------------------

## 🚀 Deploying to AWS ECR

1.  Create a private repo in **AWS ECR** named:

        deeptune/app-yourappname-base

2.  Run:

    ``` bash
    doppler run -- bash ./.deeptune/deploy_to_ecr.sh
    ```

3.  The script will:

    -   Verify AWS credentials\
    -   Auto-increment the image version tag\
    -   Build & push multi-arch Docker images (`amd64` + `arm64`)\
    -   Tag both `latest` and versioned builds

------------------------------------------------------------------------

## ⚡ Requirements

-   Base image: `ubuntu:22.04`\

-   All services started via:

    ``` bash
    /deeptune/entrypoint.sh
    ```

-   App must start within **10 seconds**\

-   All prebuild/setup tasks (migrations, compilation, etc.) must happen
    **during build**, not runtime

------------------------------------------------------------------------

## 📝 Notes & Gotchas

-   `supervisord.conf` must:
    -   Omit `supervisorctl` block\
    -   Run as `root` user\
    -   Use `nodaemon=true`\
    -   Log to `/deeptune/supervisord.log`
-   Always test login & basic app functionality after build (frontend
    may appear working even if backend is failing).

------------------------------------------------------------------------

## ✅ Completion

Once your Docker image is successfully built and deployed to ECR, please
notify your Deeptune contact for validation and integration. 🎉
