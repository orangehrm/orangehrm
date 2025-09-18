# OrangeHRM Terraform Deployment

This repository contains Terraform configurations to deploy OrangeHRM (Human Resource Management System) on Kubernetes using Minikube for local development.

## 🚀 Quick Start

```bash
# Start Minikube
minikube start

# Deploy OrangeHRM
cd terraform
terraform init
terraform apply

# Access OrangeHRM (automatically opens in browser)
# Username: admin
# Password: admin
```

## 📋 Prerequisites

- **Terraform** >= 1.4.0
- **kubectl** (Kubernetes CLI)
- **Minikube** (for local Kubernetes)
- **Docker** (for container images)

## 📚 Documentation

- **[Deployment Guide](DEPLOYMENT_GUIDE.md)** - Complete step-by-step deployment instructions
- **[Architecture Documentation](ARCHITECTURE.md)** - Detailed technical architecture and components

## 🏗️ What Gets Deployed

This Terraform configuration creates a complete OrangeHRM stack:

- **MySQL Database** - Persistent database with initialization
- **OrangeHRM Application** - Web application with automatic setup
- **Sample Data** - Optional employee and candidate data
- **Custom Domain Access** - Automatic port forwarding and browser opening

## ⚙️ Configuration

### Default Values

- **Namespace**: `orangehrm`
- **Environment**: `minikube`
- **Sample Data**: `enabled`
- **OrangeHRM Version**: `5.7`
- **MySQL Version**: `8.0`
- **Custom Domain**: `orangehrm.local:8080` (automatic port forwarding)

### Customization

Create a `terraform.tfvars` file to customize:

```hcl
namespace = "my-orangehrm"
mysql_root_password = "secure_password"
load_sample_data = true

# Custom domain for OrangeHRM access
ingress_host = "orangehrm.local"

```

## 🔧 Commands

```bash
# Initialize Terraform
terraform init

# Plan deployment
terraform plan

# Deploy
terraform apply

# Destroy deployment
terraform destroy
```

## 🐛 Troubleshooting

### Common Issues

- **Minikube not starting**: Ensure sufficient system resources (4GB RAM, 2 CPU cores)
- **Pods stuck pending**: Check node resources and storage classes
- **MySQL connection issues**: Verify MySQL pod logs and service status

### Debug Commands

```bash
# Check pod status
kubectl get pods -n orangehrm

# View logs
kubectl logs -n orangehrm deployment/orangehrm

# Check services
kubectl get services -n orangehrm
```

## 📁 Project Structure

```
terraform/
├── ARCHITECTURE.md          # Technical architecture documentation
├── DEPLOYMENT_GUIDE.md      # Step-by-step deployment guide
├── README.md               # This file
├── backend.tf              # Terraform backend configuration
├── outputs.tf              # Terraform outputs
├── providers.tf            # Provider configurations
├── resources.tf            # Main resource definitions
├── variables.tf            # Variable definitions
└── terraform.tfvars.example # Example variables file
```

## 🔒 Security Notes

- Default admin credentials are `admin/admin` - change them after first login
- Database name: `orangehrm`
- MySQL passwords are marked as sensitive in Terraform
- All containers have resource limits defined
- Services use internal networking by default

## 🆘 Support

- **Documentation**: Check the [Deployment Guide](DEPLOYMENT_GUIDE.md) and [Architecture](ARCHITECTURE.md)
- **Issues**: Create an issue in the repository
