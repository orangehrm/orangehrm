# OrangeHRM Terraform Deployment Architecture

## Overview

This Terraform configuration deploys a complete OrangeHRM (Human Resource Management System) stack on Kubernetes, specifically optimized for Minikube development environments. The deployment includes MySQL database, OrangeHRM application, and optional sample data loading.

## Architecture Components

### 1. Namespace

- **Resource**: `kubernetes_namespace.orangehrm`
- **Purpose**: Isolates all OrangeHRM resources in a dedicated namespace
- **Default**: `orangehrm`

### 2. MySQL Database

#### MySQL ConfigMap

- **Resource**: `kubernetes_config_map.mysql_init`
- **Purpose**: Contains SQL initialization script for MySQL root user configuration
- **Function**: Sets up MySQL native password authentication for root user

#### MySQL Persistent Volume Claim

- **Resource**: `kubernetes_persistent_volume_claim.mysql_pvc`
- **Purpose**: Provides persistent storage for MySQL data
- **Size**: 10Gi
- **Access Mode**: ReadWriteOnce

#### MySQL Deployment

- **Resource**: `kubernetes_deployment.mysql`
- **Image**: `mysql:8.0`
- **Features**:
  - Init container to clear stale InnoDB files
  - Health checks (liveness and readiness probes)
  - Resource limits and requests
  - Persistent volume mounting
  - Environment variables for root password and host configuration

#### MySQL Service

- **Resource**: `kubernetes_service.mysql`
- **Type**: ClusterIP
- **Port**: 3306
- **Purpose**: Internal service discovery for MySQL

### 3. OrangeHRM Application

#### OrangeHRM Install ConfigMap

- **Resource**: `kubernetes_config_map.orangehrm_install_config`
- **Purpose**: Contains CLI installation configuration for OrangeHRM
- **Configuration**:
  - Database connection settings
  - Organization information
  - Admin user credentials
  - License agreement

#### OrangeHRM ConfigMap

- **Resource**: `kubernetes_config_map.orangehrm_config`
- **Purpose**: Runtime database configuration for OrangeHRM application
- **Data**: Database host, port, name, user, and password

#### OrangeHRM Initialization Job

- **Resource**: `kubernetes_job.orangehrm_init`
- **Purpose**: One-time database initialization and OrangeHRM installation
- **Process**:
  1. Installs MySQL client
  2. Waits for MySQL to be ready
  3. Checks for existing OrangeHRM schema
  4. Runs OrangeHRM CLI installer if needed
  5. Configures database schema and admin user

#### OrangeHRM Deployment

- **Resource**: `kubernetes_deployment.orangehrm`
- **Image**: `orangehrm/orangehrm:5.7`
- **Features**:
  - Health checks (liveness and readiness probes)
  - Resource limits and requests
  - Environment variables for database connection
  - Port 80 exposure

#### OrangeHRM Service

- **Resource**: `kubernetes_service.orangehrm`
- **Type**: NodePort
- **Port**: 80
- **Purpose**: Exposes OrangeHRM application for external access

### 4. Sample Data Loading (Optional)

#### Data Scripts ConfigMap

- **Resource**: `kubernetes_config_map.data_scripts` (conditional)
- **Purpose**: Contains PHP scripts and data files for loading sample data
- **Files**:
  - `load-employees.php`: Employee data loading script
  - `load-candidates.php`: Candidate data loading script
  - `canidate-name-list.txt`: List of candidate names
  - `job-description-*.txt`: Job description files

#### Employee Data Loading Job

- **Resource**: `kubernetes_job.load_employees` (conditional)
- **Purpose**: Loads sample employee data into OrangeHRM
- **Process**:
  1. Installs MySQL client and curl
  2. Waits for MySQL and OrangeHRM to be ready
  3. Ensures database user exists
  4. Executes employee data loading script

#### Candidate Data Loading Job

- **Resource**: `kubernetes_job.load_candidates` (conditional)
- **Purpose**: Loads sample candidate and job vacancy data
- **Process**:
  1. Installs MySQL client and curl
  2. Waits for MySQL and OrangeHRM to be ready
  3. Ensures database user exists
  4. Executes candidate data loading script

### 5. Minikube Integration

#### Minikube Tunnel Provisioner

- **Resource**: `null_resource.minikube_tunnel` (conditional)
- **Purpose**: Automatically launches Minikube service tunnel after deployment
- **Process**:
  1. Checks if Minikube is available
  2. Waits for services to be ready
  3. Launches `minikube service orangehrm -n orangehrm`
  4. Opens OrangeHRM in default browser

## Resource Dependencies

The deployment follows a specific order to ensure proper initialization:

1. **Namespace** → All other resources
2. **ConfigMaps** → Referenced by deployments and jobs
3. **MySQL PVC** → MySQL deployment
4. **MySQL Deployment** → MySQL service
5. **OrangeHRM Init Job** → OrangeHRM deployment (depends on MySQL)
6. **OrangeHRM Deployment** → OrangeHRM service
7. **Data Loading Jobs** → OrangeHRM deployment and data scripts
8. **Minikube Tunnel** → OrangeHRM service and data loading jobs

## Configuration Variables

### Required Variables

- `namespace`: Kubernetes namespace for the application
- `mysql_root_password`: MySQL root password
- `mysql_database`: Database name for OrangeHRM
- `mysql_user`: Database user for OrangeHRM
- `mysql_password`: Database password for OrangeHRM user

### Optional Variables

- `public_access`: Whether to allow public access (default: false)
- `load_sample_data`: Whether to load sample data (default: true)
- `environment`: Environment type (default: minikube)
- `orangehrm_image`: OrangeHRM Docker image (default: orangehrm/orangehrm)
- `orangehrm_tag`: OrangeHRM Docker image tag (default: 5.7)
- `mysql_image`: MySQL Docker image (default: mysql)
- `mysql_tag`: MySQL Docker image tag (default: 8.0)

## Security Considerations

1. **Sensitive Data**: MySQL passwords are marked as sensitive in Terraform
2. **Default Credentials**: Admin user is created with default credentials (admin/admin)
3. **Network Security**: Services use ClusterIP/NodePort for internal communication
4. **Resource Limits**: All containers have CPU and memory limits defined

## Monitoring and Health Checks

- **MySQL**: TCP socket and MySQL admin ping probes
- **OrangeHRM**: HTTP GET probes on port 80
- **Initialization**: Jobs include proper error handling and retry logic
- **Service Readiness**: All services wait for dependencies to be ready

## Scalability

- **Horizontal Scaling**: OrangeHRM deployment can be scaled by increasing replicas
- **Vertical Scaling**: Resource limits can be adjusted in the configuration
- **Database Scaling**: MySQL can be replaced with external managed database
- **Storage Scaling**: PVC size can be increased for larger datasets

## Troubleshooting

### Common Issues

1. **MySQL Connection**: Check if MySQL service is ready and accessible
2. **OrangeHRM Initialization**: Verify CLI installer configuration
3. **Data Loading**: Ensure sample data scripts are properly mounted
4. **Minikube Tunnel**: Check if Minikube is running and accessible

### Useful Commands

```bash
# Check pod status
kubectl get pods -n orangehrm

# View logs
kubectl logs -n orangehrm deployment/orangehrm
kubectl logs -n orangehrm deployment/mysql

# Check services
kubectl get services -n orangehrm

# Port forward for testing
kubectl port-forward -n orangehrm service/orangehrm 8080:80
```