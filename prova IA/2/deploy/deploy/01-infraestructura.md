# 01 // Infraestructura AWS

## 🌐 Servicios utilizados

### EC2 (Elastic Compute Cloud)
Servidores virtuales utilizados para el entorno de **desarrollo (DEV)**. Permiten un despliegue ágil vía SCP/Rsync sin tener que compilar imágenes Docker, agilizando las pruebas y la integración continua.

- **DEV**: Una única instancia EC2 con Elastic IP, Apache2, que sirve tanto el frontend (build estático de Vue) como el backend (Symfony + PHP).
- **PROD**: Opcional como alternativa/backup, pero el entorno principal usa ECS Fargate.

### S3 (Simple Storage Service)
Servicio serverless de almacenamiento donde se aloja únicamente el web estático de la **Landing Page** con hosting estático habilitado.

- Bucket público para la landing page (HTML/CSS/JS).
- Acceso directo desde el navegador.

### RDS (Relational Database Service)
Servicio gestionado de base de datos **MySQL**. Garantiza la alta disponibilidad sin exponer los datos a Internet.

- **DEV**: Instancia compartida accesible solo desde la subred privada de la EC2.
- **PROD**: Instancia independiente, separada de la de DEV, accesible solo desde los servicios ECS.

### ECS y ECR (Elastic Container Service & Registry)

- **ECR**: Repositorio privado de imágenes Docker. Almacena las imágenes de frontend y backend con etiquetado por SHA de commit + `latest`.
- **ECS Fargate**: Motor de ejecución **serverless** que orquesta los contenedores. No hay que gestionar servidores: Fargate se encarga de la alta disponibilidad y el autoescalado.

### Application Load Balancer (ALB)
Router inteligente que decide si el tráfico de Internet va al contenedor de Vue (`/*`) o de Symfony (`/api/*`). Actúa como punto único de entrada HTTPS en PROD.

### AWS Certificate Manager (ACM)
Proporciona y renueva automáticamente certificados SSL para el ALB, garantizando navegación segura por HTTPS en producción.

### AWS Secrets Manager
Almacena credenciales sensibles (APP_SECRET, DATABASE_URL, JWT keys) y las inyecta como variables de entorno a los contenedores ECS.

---

## 🔌 Red y Seguridad

### VPC y Subnets

La arquitectura de red se compone de una **VPC** con CIDR `10.0.0.0/16`, con:

| Tipo | Subnet | CIDR | Acceso |
|------|--------|------|--------|
| Pública | `10.0.1.0/24` | AZ A | Internet Gateway |
| Pública | `10.0.2.0/24` | AZ B | Internet Gateway |
| Privada | `10.0.3.0/24` | AZ A | Solo salida vía NAT |
| Privada | `10.0.4.0/24` | AZ B | Solo salida vía NAT |

### Security Groups

Se aplica el **principio de mínimo privilegio**:

| Security Group | Función | Reglas de entrada |
|----------------|---------|-------------------|
| **alb-sg** | Público | Puertos 80 (HTTP) y 443 (HTTPS) desde `0.0.0.0/0` |
| **ecs-sg** | Privado | Puerto 80 exclusivamente desde `alb-sg` |
| **rds-sg** | Muy privado | Puerto 3306 (MySQL) exclusivamente desde `ecs-sg` |

> ⚠️ **Nota**: La EC2 de DEV también se conecta al RDS, por lo que se necesita una regla adicional en el `rds-sg` permitiendo tráfico desde el security group de la EC2.

---

## 🧠 Justificación de Decisiones Técnicas

| Decisión | Razón |
|----------|-------|
| **Arquitectura híbrida ECS Fargate + EC2** | Elimina la tarea de mantener SOs en producción (Fargate) y permite despliegue rápido sin Docker en DEV (EC2). Estrategia de *Zero Downtime Deployment* con Fargate. |
| **Landing Page en S3 + CloudFront** | Velocidad de respuesta mundial instantánea; beneficia el SEO (TTFB mínimo). Coste casi cero. |
| **Secrets Manager en lugar de .env estáticos** | Extirpa información sensible del código fuente. Evita Symfony Secrets manuales en el repositorio. |
| **ALB con routing por path** | Permite servir frontend y backend desde el mismo Load Balancer. Las reglas `/*` actúan como catch-all para el Vue Router. |
| **Base de datos separada por entorno** | Evita que las pruebas en DEV corrompan datos de producción. |
