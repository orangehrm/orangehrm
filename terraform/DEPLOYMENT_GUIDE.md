# OrangeHRM Terraform Deployment Guide

## Prerequisites

Before deploying OrangeHRM with Terraform, ensure you have the following tools installed and configured:

### Required Tools

- **Terraform** >= 1.4.0
- **kubectl** (Kubernetes command-line tool)
- **Minikube** (for local development)
- **Docker** (for container images)

### Installation Instructions

#### Terraform

```bash
# macOS (using Homebrew)
brew install terraform

# Linux (using package manager)
# Ubuntu/Debian
wget -O- https://apt.releases.hashicorp.com/gpg | gpg --dearmor | sudo tee /usr/share/keyrings/hashicorp-archive-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] https://apt.releases.hashicorp.com $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/hashicorp.list
sudo apt update && sudo apt install terraform

# Windows (using Chocolatey)
choco install terraform
```

#### kubectl

```bash
# macOS (using Homebrew)
brew install kubectl

# Linux
curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
sudo install -o root -g root -m 0755 kubectl /usr/local/bin/kubectl

# Windows (using Chocolatey)
choco install kubernetes-cli
```

#### Minikube

```bash
# macOS (using Homebrew)
brew install minikube

# Linux
curl -LO https://storage.googleapis.com/minikube/releases/latest/minikube-linux-amd64
sudo install minikube-linux-amd64 /usr/local/bin/minikube

# Windows (using Chocolatey)
choco install minikube
```

## Quick Start

### 1. Start Minikube

```bash
# Start Minikube with sufficient resources
minikube start

# Verify Minikube is running
minikube status
```

### 2. Configure kubectl

```bash
# Set kubectl context to Minikube
kubectl config use-context minikube

# Verify connection
kubectl cluster-info
```

### 3. Deploy OrangeHRM

```bash
# Navigate to terraform directory
cd terraform

# Initialize Terraform
terraform init

# Review the deployment plan
terraform plan

# Apply the configuration
terraform apply
```

### 4. Access OrangeHRM

After successful deployment, the Minikube service tunnel will automatically open OrangeHRM in your default browser.

**Default Credentials:**

- Username: `admin`
- Password: `admin`

## Detailed Deployment Steps

### Step 1: Environment Setup

1. **Start Minikube with adequate resources:**

   ```bash
   minikube start --disk-size=20g
   ```

2. **Enable required Minikube addons:**

   ```bash
   minikube addons enable storage-provisioner
   minikube addons enable default-storageclass
   ```

3. **Verify Minikube status:**
   ```bash
   minikube status
   kubectl get nodes
   ```

### Step 2: Terraform Configuration

1. **Initialize Terraform:**

   ```bash
   cd terraform
   terraform init
   ```

2. **Review configuration variables:**

   ```bash
   # View current variables
   terraform console
   > var.namespace
   > var.load_sample_data
   ```

3. **Customize variables (optional):**

   ```bash
   # Copy example variables file
   cp terraform.tfvars.example terraform.tfvars

   # Edit variables as needed
   nano terraform.tfvars
   ```

### Step 3: Deployment Execution

1. **Plan the deployment:**

   ```bash
   terraform plan
   ```

2. **Apply the configuration:**

   ```bash
   # Apply with auto-approval
   terraform apply -auto-approve

   # Or apply with confirmation prompt
   terraform apply
   ```

3. **Monitor deployment progress:**

   ```bash
   # Watch pods being created
   kubectl get pods -n orangehrm -w

   # Check deployment status
   kubectl get deployments -n orangehrm
   ```

### Step 4: Verification

1. **Check all resources:**

   ```bash
   # Verify pods are running
   kubectl get pods -n orangehrm

   # Check services
   kubectl get services -n orangehrm

   # View logs if needed
   kubectl logs -n orangehrm deployment/orangehrm
   kubectl logs -n orangehrm deployment/mysql
   ```

2. **Test application access:**

   ```bash
   # If Minikube tunnel didn't start automatically
   minikube service orangehrm -n orangehrm

   # Or use port forwarding
   kubectl port-forward -n orangehrm service/orangehrm 8080:80
   # Then open http://localhost:8080
   ```

## Configuration Options

### Custom Domain Access (Recommended)

OrangeHRM is automatically configured with a custom domain for easy access:

```hcl
# Custom domain configuration
ingress_host = "orangehrm.local"
```

**How it works:**

- **Port forwarding**: Automatically sets up `kubectl port-forward` to localhost:8080
- **Custom domain**: Configures `orangehrm.local` in your hosts file
- **Browser opening**: Automatically opens OrangeHRM in your default browser
- **Clean URL**: Access via `http://orangehrm.local:8080`

**Benefits:**

- ✅ **Simple setup**: No complex Ingress configuration needed
- ✅ **Custom domain**: Professional URL instead of IP addresses
- ✅ **Automatic**: Everything configured automatically by Terraform
- ✅ **Reliable**: Uses standard Kubernetes port forwarding

**Access OrangeHRM:**

- **URL**: `http://orangehrm.local:8080`
- **Credentials**: `admin` / `admin`
- **Database**: `orangehrm`

### Ingress Controller (Advanced - Optional)

⚠️ **Note**: Ingress with Minikube is complex on macOS and requires sudo permissions and persistent tunnels. The port forwarding method above is recommended for local development.

For production-like setup, you can enable the NGINX Ingress Controller:

```hcl
# Enable Ingress Controller
enable_ingress = true
ingress_host = "orangehrm.local"
```

**Benefits of using Ingress:**

- Custom domain name (e.g., `orangehrm.local`)
- Better URL structure
- SSL/TLS termination support
- Load balancing capabilities
- More production-like setup

**Setup Instructions:**

1. Enable Ingress in your `terraform.tfvars`:

   ```hcl
   enable_ingress = true
   ingress_host = "orangehrm.local"
   ```

2. Deploy with Terraform:

   ```bash
   terraform apply
   ```

3. Add the Minikube IP to your hosts file:

   ```bash
   # Get Minikube IP
   minikube ip

   # Add to /etc/hosts (Linux/macOS) or C:\Windows\System32\drivers\etc\hosts (Windows)
   # Example: 192.168.49.2 orangehrm.local
   ```

4. Access OrangeHRM at: `http://orangehrm.local`

### Environment Variables

Create a `terraform.tfvars` file to customize the deployment:

```hcl
# Basic Configuration
namespace = "orangehrm"
environment = "minikube"

# Database Configuration
mysql_root_password = "your_secure_password"
mysql_database = "orangehrm"
mysql_user = "orangehrm"
mysql_password = "your_secure_password"

# Application Configuration
load_sample_data = true
public_access = false

# Image Configuration
orangehrm_image = "orangehrm/orangehrm"
orangehrm_tag = "5.7"
mysql_image = "mysql"
mysql_tag = "8.0"
```

### Advanced Configuration

#### Custom Resource Limits

Modify `resources.tf` to adjust CPU and memory limits:

```hcl
resources {
  limits = {
    cpu    = "2000m"    # 2 CPU cores
    memory = "2Gi"      # 2GB RAM
  }
  requests = {
    cpu    = "1000m"    # 1 CPU core
    memory = "1Gi"      # 1GB RAM
  }
}
```

#### Custom Storage Size

Modify the PVC size in `resources.tf`:

```hcl
resources {
  requests = {
    storage = "20Gi"    # 20GB storage
  }
}
```

## Troubleshooting

### Common Issues and Solutions

#### 1. Minikube Not Starting

```bash
# Check Minikube status
minikube status

# Restart Minikube
minikube stop
minikube start

# Check system resources
minikube ssh
free -h
df -h
```

#### 2. Pods Stuck in Pending State

```bash
# Check pod events
kubectl describe pod <pod-name> -n orangehrm

# Check node resources
kubectl top nodes
kubectl describe nodes

# Check storage classes
kubectl get storageclass
```

#### 3. MySQL Connection Issues

```bash
# Check MySQL pod logs
kubectl logs -n orangehrm deployment/mysql

# Test MySQL connection
kubectl exec -it deployment/mysql -n orangehrm -- mysql -u root -p

# Check MySQL service
kubectl get service mysql -n orangehrm
```

#### 4. OrangeHRM Initialization Failed

```bash
# Check initialization job logs
kubectl logs -n orangehrm job/orangehrm-init

# Check OrangeHRM pod logs
kubectl logs -n orangehrm deployment/orangehrm

# Restart initialization job
kubectl delete job orangehrm-init -n orangehrm
terraform apply
```

#### 5. Data Loading Issues

```bash
# Check data loading job logs
kubectl logs -n orangehrm job/load-employees
kubectl logs -n orangehrm job/load-candidates

# Verify data scripts ConfigMap
kubectl get configmap data-scripts -n orangehrm -o yaml
```

### Useful Debugging Commands

```bash
# Get all resources in namespace
kubectl get all -n orangehrm

# Describe specific resource
kubectl describe deployment orangehrm -n orangehrm

# Check resource usage
kubectl top pods -n orangehrm
kubectl top nodes

# View events
kubectl get events -n orangehrm --sort-by='.lastTimestamp'

# Check persistent volumes
kubectl get pv
kubectl get pvc -n orangehrm
```

## Maintenance

### Updating the Deployment

1. **Update Terraform configuration**
2. **Plan changes:**
   ```bash
   terraform plan
   ```
3. **Apply updates:**
   ```bash
   terraform apply
   ```

### Scaling the Application

```bash
# Scale OrangeHRM deployment
kubectl scale deployment orangehrm -n orangehrm --replicas=3

# Check scaling status
kubectl get pods -n orangehrm
```

### Backup and Restore

#### Database Backup

```bash
# Create database backup
kubectl exec -it deployment/mysql -n orangehrm -- mysqldump -u root -p orangehrm > orangehrm_backup.sql
```

#### Database Restore

```bash
# Restore database from backup
kubectl exec -i deployment/mysql -n orangehrm -- mysql -u root -p orangehrm < orangehrm_backup.sql
```

## Cleanup

### Remove the Deployment

```bash
# Destroy Terraform resources
terraform destroy

# Verify cleanup
kubectl get all -n orangehrm

# Remove namespace (if not automatically removed)
kubectl delete namespace orangehrm
```

### Stop Minikube

```bash
# Stop Minikube
minikube stop

# Delete Minikube cluster (optional)
minikube delete
```

## Support and Resources

### Documentation

- [Terraform Kubernetes Provider](https://registry.terraform.io/providers/hashicorp/kubernetes/latest/docs)
- [Minikube Documentation](https://minikube.sigs.k8s.io/docs/)

### Community

- [Terraform Community](https://discuss.hashicorp.com/c/terraform-core)
- [Kubernetes Community](https://kubernetes.io/community/)

