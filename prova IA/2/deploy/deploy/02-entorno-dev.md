# 02 // Entorno de Desarrollo (DEV)

El entorno DEV está diseñado para facilitar la **integración, pruebas y validación continua** durante el desarrollo. Se prioriza la simplicidad y la velocidad del despliegue por encima del aislamiento total.

---

## 🖥️ Infraestructura DEV

### Landing Page
Se despliega en un **bucket de Amazon S3** con hosting estático habilitado, con acceso público directo desde el navegador.

### Frontend + Backend
Ambos componentes se ejecutan en una **única instancia EC2** que dispone de:

- **Elastic IP** fija
- **Apache2** como servidor web (sirve tanto el build estático de Vue como la API Symfony)
- **PHP 8.3** con extensiones necesarias
- **Composer** para dependencias PHP

### Base de datos
Instancia **Amazon RDS MySQL** compartida para el entorno DEV. La EC2 tiene acceso directo vía security groups.

### Dominio y HTTPS

| Servicio | Uso |
|----------|-----|
| **DuckDNS** o **No-IP** | Dominio dinámico para el acceso a la aplicación |
| **Certbot + Let's Encrypt** | Certificados SSL gratuitos para HTTPS en DEV |

---

## 🔄 Proceso de despliegue DEV

### Frontend (Vue 3)

El despliegue del frontend en DEV se realiza mediante **SCP** desde GitHub Actions:

1. **Build** en el runner de GitHub Actions: `npm ci && npm run build`
2. **Transferencia** de los artefactos (`dist/`) al servidor EC2 vía SCP
3. **Apache sirve** los ficheros estáticos directamente

El workflow `frontend7vision.yml` se activa automáticamente al hacer push a la rama `develop`.

### Backend (Symfony 7)

El despliegue del backend en DEV se realiza mediante **Rsync**:

1. **Sincronización** del código fuente al servidor EC2 vía Rsync (excluyendo `vendor/`, `var/cache/`, `.git/`)
2. **Instalación de dependencias** en el servidor: `composer install --no-interaction`
3. **Migraciones**: `php bin/console doctrine:migrations:migrate --no-interaction`
4. **Limpieza de caché**: `php bin/console cache:clear`

El workflow `deployBackend.yml` se activa automáticamente al hacer push a `develop`.

### Landing Page

- Subida manual o automatizada de los ficheros HTML/CSS/JS al bucket S3.
- El bucket tiene hosting estático habilitado.

---

## 📋 Requisitos DEV

- APP_ENV = `dev` (o `local`)
- Las variables de entorno se configuran en el servidor EC2 mediante `.env.local`
- La base de datos RDS debe permitir conexiones desde la IP de la EC2
- Apache2 debe tener los módulos `rewrite`, `proxy` y `proxy_http` activados

### Configuración de Apache2 para DEV

**Backend (Symfony)**: Deriva el tráfico que no apunta a ficheros estáticos hacia `index.php` (Front Controller).

**Frontend (SPA)**: RewriteRule `^ index.html [QSA,L]` para evitar errores 404 en rutas virtuales de Vue (`/peliculas`, `/profile`, etc.).
