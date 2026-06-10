# 03 // Entorno de Producción (PROD)

El entorno PROD se basa en una arquitectura **distribuida, escalable y containerizada**, utilizando servicios gestionados de AWS y contenedores Docker.

---

## 🏛️ Infraestructura PROD

| Componente | Tecnología | Ubicación |
|-----------|-----------|-----------|
| Landing Page | HTML/CSS/JS estático | AWS S3 (bucket separado) |
| Frontend | Vue 3 SPA → Docker + Apache | AWS ECS Fargate |
| Backend | Symfony 7 API → Docker + Apache | AWS ECS Fargate |
| Base de datos | MySQL | AWS RDS (instancia independiente) |
| Load Balancer | ALB | AWS Application Load Balancer |
| Registro de imágenes | Docker images | Amazon ECR |
| Certificados SSL | ACM | AWS Certificate Manager |

---

## 📦 Containerización

### Dockerfile Backend

```dockerfile
FROM php:8.3-apache

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    libzip-dev libicu-dev zip unzip git \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activar rewrite para URLs amigables
RUN a2enmod rewrite

# Configuración de producción
RUN echo "display_errors=off" >> /usr/local/etc/php/conf.d/prod_errors.ini

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/backend
COPY . .

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
```

### Entrypoint (Backend)

El script más crítico del backend. Se ejecuta cada vez que el contenedor se inicia:

```bash
#!/bin/sh
set -e

if [ "$APP_ENV" = "prod" ]; then
  # Limpieza y precalentamiento de caché
  php bin/console cache:clear --env=prod --no-debug
  php bin/console cache:warmup --env=prod
  chown -R www-data:www-data var/cache var/log
  chmod -R 777 var/cache var/log
fi

# Genera claves JWT al vuelo
mkdir -p config/jwt
chown -R www-data:www-data config/jwt
rm -f config/jwt/*.pem
php bin/console lexik:jwt:generate-keypair --no-interaction

# Migraciones automáticas
php bin/console doctrine:database:create --if-not-exists --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec apache2-foreground
```

### Dockerfile Frontend (Multi-stage)

```dockerfile
FROM node:20 AS build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM httpd:2.4
COPY --from=build /app/dist/ /usr/local/apache2/htdocs/
COPY docker/apache.conf /usr/local/apache2/conf/extra/spa.conf
RUN sed -i 's/#LoadModule rewrite_module modules\/mod_rewrite.so/LoadModule rewrite_module modules\/mod_rewrite.so/' /usr/local/apache2/conf/httpd.conf
RUN echo "Include conf/extra/spa.conf" >> /usr/local/apache2/conf/httpd.conf
```

---

## 🌐 Application Load Balancer (ALB)

El ALB actúa como **punto único de entrada** al sistema en producción.

### Reglas de routing

| Prioridad | Path | Target Group |
|-----------|------|-------------|
| 1 | `/api/*` | `backend-tg` |
| 2 | `/admin*` | `backend-tg` |
| 3 | `/login*` | `backend-tg` |
| 4 | `/logout` | `backend-tg` |
| 5 | `/css*` | `backend-tg` |
| 6 | `/js*` | `backend-tg` |
| 7 | `/lib*` | `backend-tg` |
| 8 | `/img/avatars/*` | `backend-tg` |
| 9 | `/img/posters/*` | `backend-tg` |
| 10 | `/*` | `frontend-tg` |
| Default | Cualquier otra | `frontend-tg` |

> **Nota**: La regla `/*` actúa como catch-all. Cualquier URL que no coincida con las reglas anteriores se envía al Frontend. Esto es vital para el Vue Router interno, que gestiona rutas como `/home` o `/profile` en el navegador sin que el Load Balancer devuelva un 404.

### HTTPS

- El ALB escucha en los puertos **80** (HTTP) y **443** (HTTPS).
- Todo el tráfico HTTP se redirige a HTTPS.
- Certificados gestionados por **AWS Certificate Manager (ACM)**.

---

## 🗄️ Base de datos RDS (PROD)

- Instancia **MySQL** independiente de la de DEV.
- Ubicada en **subred privada** sin acceso directo desde Internet.
- El único componente que puede conectarse es el **backend (ECS)** vía security groups.

---

## ⚙️ ECS Fargate

### Clúster
- Nombre: `grupXX-cluster`
- Tipo de ejecución: **Fargate** (serverless, sin ASG manual)

### Servicios

| Servicio | Contenedor | Puerto | Auto Scaling |
|----------|-----------|--------|-------------|
| `frontend-service` | Apache + VueJS build | 80 | 1-4 contenedores (CPU) |
| `backend-service` | Apache + Symfony | 80 | 1-4 contenedores (CPU) |

### Task Definitions

- **backend-task**: 1 vCPU, 3 GB RAM. Variables de entorno vía **ValueFrom** (Secrets Manager).
- **frontend-task**: Recursos ajustados al build estático. Puerto 80.

### Auto Scaling
Política de **Target Tracking** basada en CPU: mínimo 1 contenedor, máximo 4.

---

## 🔗 Comunicación y CORS

El frontend y el backend operan en **dominios diferentes** (`grupXX.duckdns.org` vs `api.grupXX.duckdns.org`). Hay que configurar **CORS** en el backend Symfony para permitir:

- Origen explícito del frontend
- Métodos: GET, POST, PUT, DELETE, OPTIONS
- Headers: Authorization, Content-Type
- Credentials: true

---

## 📊 Flujo completo en PROD

```
Usuario → HTTPS → DuckDNS → ALB → Reglas de routing
                                       │
                  ┌────────────────────┤
                  ▼                    ▼
          Frontend ECS           Backend ECS
          (Vue + Apache)         (Symfony + Apache)
                  │                    │
                  └──── JWT Auth ──────┘
                                       │
                                       ▼
                                  AWS RDS (MySQL)
```
