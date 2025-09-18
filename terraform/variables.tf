variable "namespace" {
  description = "namespace for the application"
  type        = string
  default     = "orangehrm"
}

variable "public_access" {
  description = "Whether to allow public access to the application (false = internal only, true = public access)"
  type        = bool
  default     = false
}

variable "mysql_root_password" {
  description = "MySQL root password"
  type        = string
  default     = "orangehrm"
  sensitive   = true
}

variable "mysql_database" {
  description = "MySQL database name for OrangeHRM"
  type        = string
  default     = "orangehrm"
}

variable "mysql_user" {
  description = "MySQL user for OrangeHRM"
  type        = string
  default     = "orangehrm"
}

variable "mysql_password" {
  description = "MySQL password for OrangeHRM user"
  type        = string
  default     = "orangehrm"
  sensitive   = true
}

variable "orangehrm_image" {
  description = "OrangeHRM Docker image"
  type        = string
  default     = "orangehrm/orangehrm"
}

variable "orangehrm_tag" {
  description = "OrangeHRM Docker image tag"
  type        = string
  default     = "5.7"
}

variable "mysql_image" {
  description = "MySQL Docker image"
  type        = string
  default     = "mysql"
}

variable "mysql_tag" {
  description = "MySQL Docker image tag"
  type        = string
  default     = "8.0"
}

variable "load_sample_data" {
  description = "Whether to load sample employee and recruitment data"
  type        = bool
  default     = true
}

variable "environment" {
  description = "Environment type (minikube, gke, etc.)"
  type        = string
  default     = "minikube"
}

variable "enable_ingress" {
  description = "Whether to enable NGINX Ingress Controller (Minikube only)"
  type        = bool
  default     = true
}

variable "ingress_host" {
  description = "Hostname for the Ingress (e.g., orangehrm.local)"
  type        = string
  default     = "orangehrm.local"
}

