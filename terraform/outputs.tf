output "endpoint" {
  description = "The application endpoint URL"
  value       = var.public_access ? "http://${kubernetes_service.orangehrm.status.0.load_balancer.0.ingress.0.ip}" : "http://${kubernetes_service.orangehrm.spec.0.cluster_ip}:${kubernetes_service.orangehrm.spec.0.port.0.port}"
}

output "namespace" {
  description = "The namespace used for the application"
  value       = var.namespace
}

output "mysql_service" {
  description = "MySQL service name"
  value       = kubernetes_service.mysql.metadata.0.name
}

output "orangehrm_service" {
  description = "OrangeHRM service name"
  value       = kubernetes_service.orangehrm.metadata.0.name
}

output "admin_credentials" {
  description = "Default admin credentials"
  value = {
    username = "admin"
    password = "admin"
  }
  sensitive = false
}

output "orangehrm_url" {
  description = "OrangeHRM access URL with port forwarding"
  value       = var.environment == "minikube" ? "http://${var.ingress_host}:8080" : null
}

output "ingress_ip" {
  description = "Ingress IP address (if enabled)"
  value       = var.enable_ingress && var.environment == "minikube" ? try(kubernetes_ingress_v1.orangehrm[0].status[0].load_balancer[0].ingress[0].ip, null) : null
}
