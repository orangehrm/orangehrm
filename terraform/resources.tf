# Create namespace for OrangeHRM
resource "kubernetes_namespace" "orangehrm" {
  metadata {
    name = var.namespace
    labels = {
      app = "orangehrm"
    }
  }
}

# MySQL ConfigMap for initialization
resource "kubernetes_config_map" "mysql_init" {
  metadata {
    name      = "mysql-init"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
  }
  data = {
    "init.sql" = <<-EOT
      ALTER USER IF EXISTS 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${var.mysql_root_password}';
      ALTER USER IF EXISTS 'root'@'%' IDENTIFIED WITH mysql_native_password BY '${var.mysql_root_password}';
      FLUSH PRIVILEGES;
    EOT
  }
  depends_on = [kubernetes_namespace.orangehrm]
}

# OrangeHRM CLI Install ConfigMap
resource "kubernetes_config_map" "orangehrm_install_config" {
  metadata {
    name      = "orangehrm-install-config"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
  }
  data = {
    "cli_install_config.yaml" = <<-EOT
database:
  hostName: mysql
  hostPort: 3306
  databaseName: ${var.mysql_database}
  privilegedDatabaseUser: root
  privilegedDatabasePassword: ${var.mysql_root_password}
  useSameDbUserForOrangeHRM: n
  orangehrmDatabaseUser: ${var.mysql_user}
  orangehrmDatabasePassword: ${var.mysql_password}
  isExistingDatabase: n
  enableDataEncryption: n

organization:
  name: OrangeHRM
  country: US

admin:
  adminUserName: admin
  adminPassword: admin
  adminEmployeeFirstName: OrangeHRM
  adminEmployeeLastName: Admin
  workEmail: admin@example.com
  contactNumber: ~
  registrationConsent: true

license:
  agree: y
    EOT
  }
  depends_on = [kubernetes_namespace.orangehrm]
}

# MySQL Persistent Volume Claim
resource "kubernetes_persistent_volume_claim" "mysql_pvc" {
  metadata {
    name      = "mysql-pvc"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
  }
  spec {
    access_modes = ["ReadWriteOnce"]
    resources {
      requests = {
        storage = "10Gi"
      }
    }
  }
  depends_on = [kubernetes_namespace.orangehrm]
}

# MySQL Deployment
resource "kubernetes_deployment" "mysql" {
  metadata {
    name      = "mysql"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
    labels = {
      app = "mysql"
    }
  }
  spec {
    replicas = 1
    selector {
      match_labels = {
        app = "mysql"
      }
    }
    template {
      metadata {
        labels = {
          app = "mysql"
        }
      }
      spec {
        # Clear stale InnoDB files from previous runs to avoid lock errors on PVCs
        init_container {
          name    = "mysql-data-reset"
          image   = "busybox:1.36"
          command = ["/bin/sh", "-c", "rm -rf /var/lib/mysql/*"]
          volume_mount {
            name       = "mysql-storage"
            mount_path = "/var/lib/mysql"
          }
        }
        container {
          image = "${var.mysql_image}:${var.mysql_tag}"
          name  = "mysql"
          port {
            container_port = 3306
          }
          env {
            name  = "MYSQL_ROOT_PASSWORD"
            value = var.mysql_root_password
          }
          env {
            name  = "MYSQL_ROOT_HOST"
            value = "%"
          }
          # Mount initialization script
          volume_mount {
            name       = "mysql-init"
            mount_path = "/docker-entrypoint-initdb.d"
          }
          # Mount persistent storage
          volume_mount {
            name       = "mysql-storage"
            mount_path = "/var/lib/mysql"
          }
          # Health checks
          liveness_probe {
            tcp_socket {
              port = 3306
            }
            initial_delay_seconds = 30
            period_seconds        = 10
            timeout_seconds       = 10
            failure_threshold     = 3
          }
          readiness_probe {
            exec {
              command = ["mysqladmin", "ping", "-h", "localhost"]
            }
            initial_delay_seconds = 5
            period_seconds        = 5
            timeout_seconds       = 10
            failure_threshold     = 3
          }
          # Resource limits
          resources {
            limits = {
              cpu    = "1000m"
              memory = "1Gi"
            }
            requests = {
              cpu    = "500m"
              memory = "512Mi"
            }
          }
        }
        # Volume for initialization script
        volume {
          name = "mysql-init"
          config_map {
            name = kubernetes_config_map.mysql_init.metadata[0].name
          }
        }
        # Volume for persistent storage
        volume {
          name = "mysql-storage"
          persistent_volume_claim {
            claim_name = kubernetes_persistent_volume_claim.mysql_pvc.metadata[0].name
          }
        }
      }
    }
  }
  depends_on = [kubernetes_namespace.orangehrm, kubernetes_config_map.mysql_init, kubernetes_persistent_volume_claim.mysql_pvc]
}

# MySQL Service
resource "kubernetes_service" "mysql" {
  metadata {
    name      = "mysql"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
    labels = {
      app = "mysql"
    }
  }
  spec {
    selector = {
      app = "mysql"
    }
    port {
      name        = "mysql"
      port        = 3306
      target_port = 3306
    }
    type = "ClusterIP"
  }
  depends_on = [kubernetes_deployment.mysql]
}

# Job to initialize OrangeHRM database
resource "kubernetes_job" "orangehrm_init" {
  metadata {
    name      = "orangehrm-init"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
  }
  spec {
    template {
      metadata {
        labels = {
          app = "orangehrm-init"
        }
      }
      spec {
        restart_policy = "Never"
        container {
          name    = "orangehrm-init"
          image   = "${var.orangehrm_image}:${var.orangehrm_tag}"
          command = ["/bin/bash", "-c"]
          args = [
            <<-EOT
              # Install MySQL client (default-mysql-client provides mysql CLI on Debian)
              apt-get update && apt-get install -y default-mysql-client
              
              # Wait for MySQL to be ready
              echo "Waiting for MySQL to be ready..."
              until mysql_output=$(mysql -h mysql --ssl=0 -u root -p${var.mysql_root_password} -e "SELECT 1;" 2>&1); do
                rc=$?
                echo "MySQL not ready yet, waiting... (rc=$rc)"
                echo "mysql error: $mysql_output"
                sleep 5
              done
              echo "MySQL is ready!"

              # Detect existing OrangeHRM schema and skip installer if present
              echo "Checking for existing OrangeHRM installation..."
              existing_entries=$(mysql -N -B -h mysql --ssl=0 -u root -p${var.mysql_root_password} \
                -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${var.mysql_database}';")
              if [ "$existing_entries" -gt 0 ]; then
                echo "Existing OrangeHRM schema detected, skipping CLI installer."
                exit 0
              fi
              
              # Copy config file to the right location
              cp /config/cli_install_config.yaml /var/www/html/installer/cli_install_config.yaml
              
              # Run OrangeHRM CLI installation
              echo "Initializing OrangeHRM database..."
              cd /var/www/html/installer
              php cli_install.php
              
              echo "OrangeHRM database initialization completed!"
            EOT
          ]
          env {
            name  = "DB_HOST"
            value = "mysql"
          }
          env {
            name  = "DB_PORT"
            value = "3306"
          }
          env {
            name  = "DB_NAME"
            value = var.mysql_database
          }
          env {
            name  = "DB_USER"
            value = var.mysql_user
          }
          env {
            name  = "DB_PASS"
            value = var.mysql_password
          }
          env {
            name  = "MYSQL_ROOT_PASSWORD"
            value = var.mysql_root_password
          }
          volume_mount {
            name       = "install-config"
            mount_path = "/config"
          }
          # Resource limits
          resources {
            limits = {
              cpu    = "1000m"
              memory = "1Gi"
            }
            requests = {
              cpu    = "500m"
              memory = "512Mi"
            }
          }
        }
        volume {
          name = "install-config"
          config_map {
            name = kubernetes_config_map.orangehrm_install_config.metadata[0].name
          }
        }
      }
    }
    backoff_limit              = 3
    ttl_seconds_after_finished = 300
  }
  depends_on = [kubernetes_deployment.mysql, kubernetes_config_map.orangehrm_install_config]
}

# OrangeHRM ConfigMap for database configuration
resource "kubernetes_config_map" "orangehrm_config" {
  metadata {
    name      = "orangehrm-config"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
  }
  data = {
    "db_host" = "mysql"
    "db_port" = "3306"
    "db_name" = var.mysql_database
    "db_user" = var.mysql_user
    "db_pass" = var.mysql_password
  }
  depends_on = [kubernetes_namespace.orangehrm]
}

# OrangeHRM Deployment
resource "kubernetes_deployment" "orangehrm" {
  metadata {
    name      = "orangehrm"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
    labels = {
      app = "orangehrm"
    }
  }
  spec {
    replicas = 1
    selector {
      match_labels = {
        app = "orangehrm"
      }
    }
    template {
      metadata {
        labels = {
          app = "orangehrm"
        }
      }
      spec {
        container {
          image = "${var.orangehrm_image}:${var.orangehrm_tag}"
          name  = "orangehrm"
          port {
            container_port = 80
          }
          env {
            name  = "DB_HOST"
            value = "mysql"
          }
          env {
            name  = "DB_PORT"
            value = "3306"
          }
          env {
            name  = "DB_NAME"
            value = var.mysql_database
          }
          env {
            name  = "DB_USER"
            value = var.mysql_user
          }
          env {
            name  = "DB_PASS"
            value = var.mysql_password
          }
          # Health checks
          liveness_probe {
            http_get {
              path = "/"
              port = 80
            }
            initial_delay_seconds = 60
            period_seconds        = 10
            timeout_seconds       = 10
            failure_threshold     = 3
          }
          readiness_probe {
            http_get {
              path = "/"
              port = 80
            }
            initial_delay_seconds = 30
            period_seconds        = 5
            timeout_seconds       = 10
            failure_threshold     = 3
          }
          # Resource limits
          resources {
            limits = {
              cpu    = "1000m"
              memory = "1Gi"
            }
            requests = {
              cpu    = "500m"
              memory = "512Mi"
            }
          }
        }
      }
    }
  }
  depends_on = [kubernetes_job.orangehrm_init, kubernetes_config_map.orangehrm_config]
}

# OrangeHRM Service
resource "kubernetes_service" "orangehrm" {
  metadata {
    name      = "orangehrm"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
    annotations = var.environment == "gke" && !var.public_access ? {
      "networking.gke.io/load-balancer-type" = "Internal"
    } : {}
    labels = {
      app = "orangehrm"
    }
  }
  spec {
    selector = {
      app = "orangehrm"
    }
    port {
      name        = "http"
      port        = 80
      target_port = 80
    }
    type = "NodePort"
  }
  depends_on = [kubernetes_deployment.orangehrm]
}

# ConfigMap for data loading scripts
resource "kubernetes_config_map" "data_scripts" {
  count = var.load_sample_data ? 1 : 0
  metadata {
    name      = "data-scripts"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
  }
  data = {
    "load-employees.php"     = file("${path.module}/../devTools/load/general/load-employees.php")
    "load-candidates.php"    = file("${path.module}/../devTools/load/recruitment/load-candidates.php")
    "canidate-name-list.txt" = file("${path.module}/candidate-name-list.txt")
    "job-description-1.txt"  = file("${path.module}/../devTools/load/recruitment/job-description-1.txt")
    "job-description-2.txt"  = file("${path.module}/../devTools/load/recruitment/job-description-2.txt")
    "job-description-3.txt"  = file("${path.module}/../devTools/load/recruitment/job-description-3.txt")
    "job-description-4.txt"  = file("${path.module}/../devTools/load/recruitment/job-description-4.txt")
    "job-description-5.txt"  = file("${path.module}/../devTools/load/recruitment/job-description-5.txt")
  }
  depends_on = [kubernetes_namespace.orangehrm]
}

# Job to load employee data
resource "kubernetes_job" "load_employees" {
  count = var.load_sample_data ? 1 : 0
  metadata {
    name      = "load-employees"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
  }
  spec {
    template {
      metadata {
        labels = {
          app = "data-loader"
        }
      }
      spec {
        restart_policy = "Never"
        container {
          name    = "data-loader"
          image   = "${var.orangehrm_image}:${var.orangehrm_tag}"
          command = ["/bin/bash", "-c"]
          args = [
            <<-EOT
              set -e
              export DEBIAN_FRONTEND=noninteractive

              # Install curl and MySQL client binary
              apt-get update && apt-get install -y --no-install-recommends curl default-mysql-client
              rm -rf /var/lib/apt/lists/*

              WORK_DIR=$(mktemp -d)
              trap 'rm -rf "$WORK_DIR"' EXIT
              cp -r /scripts/. "$WORK_DIR/"

              # Wait for MySQL to be ready using root credentials
              echo "Waiting for MySQL to be ready..."
              until mysql -h mysql --ssl=0 -u root -p${var.mysql_root_password} -e "SELECT 1;" > /dev/null 2>&1; do
                echo "MySQL not ready yet, waiting..."
                sleep 5
              done
              echo "MySQL is ready!"

              # Ensure application database and user exist (idempotent)
              echo "Ensuring OrangeHRM user '${var.mysql_user}' exists..."
              mysql -h mysql --ssl=0 -u root -p${var.mysql_root_password} <<-SQL
                CREATE DATABASE IF NOT EXISTS ${var.mysql_database};
                CREATE USER IF NOT EXISTS '${var.mysql_user}'@'%' IDENTIFIED BY '${var.mysql_password}';
                ALTER USER '${var.mysql_user}'@'%' IDENTIFIED WITH mysql_native_password BY '${var.mysql_password}';
                GRANT ALL PRIVILEGES ON ${var.mysql_database}.* TO '${var.mysql_user}'@'%';
                FLUSH PRIVILEGES;
              SQL

              # Wait for OrangeHRM to be ready
              echo "Waiting for OrangeHRM to be ready..."
              until curl_output=$(curl -f http://orangehrm/ 2>&1 >/dev/null); do
                rc=$?
                echo "OrangeHRM not ready yet, waiting... (rc=$rc)"
                echo "curl error: $curl_output"
                sleep 10
              done
              echo "OrangeHRM is ready!"

              # Load employee data
              echo "Loading employee data..."
              DB_HOST=mysql DB_USER=${var.mysql_user} DB_PASS=${var.mysql_password} DB_NAME=${var.mysql_database} \
                php "$WORK_DIR/load-employees.php"

              echo "Employee data loading completed!"
            EOT
          ]
          env {
            name  = "DB_HOST"
            value = "mysql"
          }
          env {
            name  = "DB_PORT"
            value = "3306"
          }
          env {
            name  = "DB_NAME"
            value = var.mysql_database
          }
          env {
            name  = "DB_USER"
            value = var.mysql_user
          }
          env {
            name  = "DB_PASS"
            value = var.mysql_password
          }
          env {
            name  = "MYSQL_ROOT_PASSWORD"
            value = var.mysql_root_password
          }
          volume_mount {
            name       = "data-scripts"
            mount_path = "/scripts"
            read_only  = true
          }
          # Resource limits
          resources {
            limits = {
              cpu    = "500m"
              memory = "512Mi"
            }
            requests = {
              cpu    = "100m"
              memory = "128Mi"
            }
          }
        }
        volume {
          name = "data-scripts"
          config_map {
            name = kubernetes_config_map.data_scripts[0].metadata[0].name
          }
        }
      }
    }
    backoff_limit              = 3
    ttl_seconds_after_finished = 300
  }
  depends_on = [kubernetes_deployment.orangehrm, kubernetes_config_map.data_scripts, kubernetes_job.orangehrm_init]
}

# Job to load candidate data
resource "kubernetes_job" "load_candidates" {
  count = var.load_sample_data ? 1 : 0
  metadata {
    name      = "load-candidates"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
  }
  spec {
    template {
      metadata {
        labels = {
          app = "data-loader"
        }
      }
      spec {
        restart_policy = "Never"
        container {
          name    = "data-loader"
          image   = "${var.orangehrm_image}:${var.orangehrm_tag}"
          command = ["/bin/bash", "-c"]
          args = [
            <<-EOT
              set -e
              export DEBIAN_FRONTEND=noninteractive

              # Install curl and MySQL client binary
              apt-get update && apt-get install -y --no-install-recommends curl default-mysql-client
              rm -rf /var/lib/apt/lists/*

              WORK_DIR=$(mktemp -d)
              trap 'rm -rf "$WORK_DIR"' EXIT
              cp -r /scripts/. "$WORK_DIR/"

              # Wait for MySQL to be ready using root credentials
              echo "Waiting for MySQL to be ready..."
              until mysql_output=$(mysql -h mysql --ssl=0 -u root -p${var.mysql_root_password} -e "SELECT 1;" 2>&1); do
                rc=$?
                echo "MySQL not ready yet, waiting... (rc=$rc)"
                echo "mysql error: $mysql_output"
                sleep 5
              done
              echo "MySQL is ready!"

              # Ensure application database and user exist (idempotent)
              echo "Ensuring OrangeHRM user '${var.mysql_user}' exists..."
              mysql -h mysql --ssl=0 -u root -p${var.mysql_root_password} <<-SQL
                CREATE DATABASE IF NOT EXISTS ${var.mysql_database};
                CREATE USER IF NOT EXISTS '${var.mysql_user}'@'%' IDENTIFIED BY '${var.mysql_password}';
                ALTER USER '${var.mysql_user}'@'%' IDENTIFIED WITH mysql_native_password BY '${var.mysql_password}';
                GRANT ALL PRIVILEGES ON ${var.mysql_database}.* TO '${var.mysql_user}'@'%';
                FLUSH PRIVILEGES;
              SQL

              # Wait for OrangeHRM to be ready
              echo "Waiting for OrangeHRM to be ready..."
              until curl_output=$(curl -f http://orangehrm/ 2>&1 >/dev/null); do
                rc=$?
                echo "OrangeHRM not ready yet, waiting... (rc=$rc)"
                echo "curl error: $curl_output"
                sleep 10
              done
              echo "OrangeHRM is ready!"

              # Load candidate data
              echo "Loading candidate data..."
              DB_HOST=mysql DB_USER=${var.mysql_user} DB_PASS=${var.mysql_password} DB_NAME=${var.mysql_database} \
                php "$WORK_DIR/load-candidates.php"

              echo "Candidate data loading completed!"
            EOT
          ]
          env {
            name  = "DB_HOST"
            value = "mysql"
          }
          env {
            name  = "DB_PORT"
            value = "3306"
          }
          env {
            name  = "DB_NAME"
            value = var.mysql_database
          }
          env {
            name  = "DB_USER"
            value = var.mysql_user
          }
          env {
            name  = "DB_PASS"
            value = var.mysql_password
          }
          env {
            name  = "MYSQL_ROOT_PASSWORD"
            value = var.mysql_root_password
          }
          volume_mount {
            name       = "data-scripts"
            mount_path = "/scripts"
            read_only  = true
          }
          # Resource limits
          resources {
            limits = {
              cpu    = "500m"
              memory = "512Mi"
            }
            requests = {
              cpu    = "100m"
              memory = "128Mi"
            }
          }
        }
        volume {
          name = "data-scripts"
          config_map {
            name = kubernetes_config_map.data_scripts[0].metadata[0].name
          }
        }
      }
    }
    backoff_limit              = 3
    ttl_seconds_after_finished = 300
  }
  depends_on = [kubernetes_job.load_employees, kubernetes_config_map.data_scripts]
}

# Provisioner to setup port forwarding and custom domain after deployment
resource "null_resource" "orangehrm_access" {
  count = var.environment == "minikube" ? 1 : 0

  provisioner "local-exec" {
    command = <<-EOT
      # Check if kubectl is available
      if command -v kubectl >/dev/null 2>&1; then
        echo "🌐 Setting up OrangeHRM access..."
        echo ""
        
        # Configure hosts file for custom domain
        HOSTS_ENTRY="127.0.0.1 ${var.ingress_host}"
        if ! grep -q "${var.ingress_host}" /etc/hosts; then
          echo "📝 Adding ${var.ingress_host} to /etc/hosts..."
          echo "$HOSTS_ENTRY" | sudo tee -a /etc/hosts
        else
          echo "✅ ${var.ingress_host} already configured in /etc/hosts"
        fi
        
        echo ""
        echo "🔌 Starting port forwarding to localhost:8080..."
        echo "OrangeHRM will be available at: http://${var.ingress_host}:8080"
        echo ""
        echo "🚀 Opening OrangeHRM in your browser..."
        sleep 3
        
        # Start port forwarding in background
        kubectl port-forward -n ${var.namespace} service/orangehrm 8080:80 &
        PORT_FORWARD_PID=$!
        
        # Wait a moment for port forwarding to be ready
        sleep 5
        
        # Open browser
        open "http://${var.ingress_host}:8080" 2>/dev/null || xdg-open "http://${var.ingress_host}:8080" 2>/dev/null || echo "Please open http://${var.ingress_host}:8080 in your browser"
        
        echo ""
        echo "✅ OrangeHRM is now accessible at: http://${var.ingress_host}:8080"
        echo "🔑 Default credentials: admin / admin"
        echo ""
        echo "📝 To stop port forwarding, run: kill $PORT_FORWARD_PID"
        echo "   Or find the process with: ps aux | grep 'kubectl port-forward'"
        
      else
        echo "⚠️  kubectl not found. You can access OrangeHRM using:"
        echo "   kubectl port-forward -n ${var.namespace} service/orangehrm 8080:80"
        echo "   Then open http://localhost:8080 in your browser"
      fi
    EOT
  }

  depends_on = [
    kubernetes_service.orangehrm,
    kubernetes_job.load_candidates
  ]
}
