# NGINX Ingress Controller for Minikube
# This resource enables the NGINX Ingress Controller addon in Minikube
resource "null_resource" "enable_ingress_addon" {
  count = var.environment == "minikube" && var.enable_ingress ? 1 : 0

  provisioner "local-exec" {
    command = <<-EOT
      echo "🔧 Enabling NGINX Ingress Controller addon in Minikube..."
      minikube addons enable ingress
      
      echo "⏳ Waiting for Ingress Controller to be ready..."
      kubectl wait --namespace ingress-nginx \
        --for=condition=ready pod \
        --selector=app.kubernetes.io/component=controller \
        --timeout=300s
      
      echo "✅ NGINX Ingress Controller is ready!"
    EOT
  }

  depends_on = [kubernetes_namespace.orangehrm]
}

# Ingress resource for OrangeHRM
resource "kubernetes_ingress_v1" "orangehrm" {
  count = var.environment == "minikube" && var.enable_ingress ? 1 : 0

  metadata {
    name      = "orangehrm-ingress"
    namespace = kubernetes_namespace.orangehrm.metadata[0].name
    annotations = {
      "nginx.ingress.kubernetes.io/rewrite-target"     = "/"
      "nginx.ingress.kubernetes.io/ssl-redirect"       = "false"
      "nginx.ingress.kubernetes.io/force-ssl-redirect" = "false"
    }
  }

  spec {
    ingress_class_name = "nginx"

    rule {
      host = var.ingress_host

      http {
        path {
          path      = "/"
          path_type = "Prefix"

          backend {
            service {
              name = kubernetes_service.orangehrm.metadata[0].name
              port {
                number = 80
              }
            }
          }
        }
      }
    }
  }

  depends_on = [
    kubernetes_service.orangehrm,
    null_resource.enable_ingress_addon
  ]
}
