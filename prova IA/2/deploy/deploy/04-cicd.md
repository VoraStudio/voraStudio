# 04 // CI/CD con GitHub Actions

## 🔄 Filosofía DevOps

El proyecto implementa un sistema completo de **integración y despliegue continuo (CI/CD)** mediante **GitHub Actions**:

- **Continuous Integration (CI)**: Integración frecuente de cambios con build y validación automática.
- **Continuous Deployment (CD)**: Despliegue automático en los entornos DEV y PROD sin intervención manual.

---

## 📁 Estructura de repositorios

El proyecto se divide en **tres repositorios independientes** en GitHub:

| Repositorio | Rol | Rama principal |
|-------------|-----|----------------|
| `landing-page-grup-5` | Landing page estática | `main` |
| `frontend-grup-5` | SPA Vue 3 | `main` |
| `backend-grup-5` | API Symfony 7 | `main` |

---

## 🌿 Estrategia GitFlow

Se utiliza **GitFlow** como metodología de control de versiones:

| Rama | Propósito |
|------|-----------|
| `main` | Versión estable en producción |
| `develop` | Integración del desarrollo |
| `feature/*` | Nuevas funcionalidades |
| `release/*` | Preparación de versiones |
| `hotfix/*` | Correcciones urgentes en producción |

---

## ⚙️ Workflows del Backend

### 1. `deployBackend.yml` — Despliegue a EC2 (DEV + PROD)

**Activación**: Push a `develop` o `main`

Utiliza un operador ternario para detectar el entorno:

```yaml
${{ github.ref_name == 'main' && 'prod' || 'dev' }}
```

**Flujo:**

1. **Selección de entorno** — Lee `github.ref_name` para decidir si es DEV o PROD
2. **Instalación de dependencias** — DEV: `composer install` | PROD: `composer install --no-dev --optimize-autoloader`
3. **Transferencia vía Rsync** — Excluye `vendor/`, `var/cache/`, `.env.local`
4. **Comandos remotos (SSH)** — Limpieza de caché, migraciones Doctrine, permisos: DEV `chmod -R 777 var/` | PROD `setfacl` (permisos ACL estrictos)

### 2. `desplegament-prod.yml` — Despliegue a ECS Fargate (PROD)

**Activación**: Push a `main`

**Flujo:**

1. **Autenticación AWS** — `aws-actions/configure-aws-credentials`
2. **Build de la imagen Docker** — Usa el `Dockerfile` del proyecto
3. **Etiquetado** — `${{ github.sha }}` (rollbacks) + `latest` (despliegue automático ECS)
4. **Push a Amazon ECR** — `docker push`
5. **Forzar nuevo despliegue a ECS** — `update-service --force-new-deployment`

---

## ⚙️ Workflows del Frontend

### 1. `frontend7vision.yml` — Despliegue SCP a EC2 (DEV + PROD)

**Activación**: Push a `develop` o `main`

**Flujo:**

1. **Instalación de dependencias** — `npm ci`
2. **Build de producción** — `npm run build`
3. **Transferencia vía SCP** — `appleboy/scp-action` copia `dist/` al servidor EC2
4. **Detección de entorno** — Ternario `${{ github.ref == 'refs/heads/main' }}` decide secrets DEV o PROD

### 2. `desplegament-prod.yml` — Despliegue a ECS Fargate (PROD)

Comparte la misma lógica que el backend. El repositorio del frontend tiene su propio workflow con el mismo nombre.

---

## 🔧 Optimización con filtros de path

Para evitar builds innecesarios, los workflows utilizan filtros de path:

```yaml
paths:
  - 'backend/backend-grup-5/**'
```

El doble asterisco (`**`) actúa como wildcard recursivo. Si solo cambian ficheros del frontend, el workflow del backend **no se ejecuta**.

---

## 🏷️ Estrategia de etiquetado de imágenes

Cuando se hace push a ECR, cada imagen se sube **dos veces**:

1. **Con el SHA del commit**: `${{ github.sha }}` → mantiene historial exacto para rollbacks
2. **Como `latest`**: se sobrescribe → el ECS Service siempre tira de `latest`

> **¿Por qué?** Sin la etiqueta `latest`, los contenedores de ECS nunca se actualizarían automáticamente. Sin el SHA, no podríamos hacer rollback a una versión anterior.

---

## 📋 Tabla resumen de Workflows

| Repositorio | Workflow | Entorno | Método | Rama |
|-------------|----------|---------|--------|------|
| `backend-grup-5` | `deployBackend.yml` | DEV + PROD | EC2 (Rsync) | `develop` / `main` |
| `backend-grup-5` | `desplegament-prod.yml` | PROD | ECS Fargate | `main` |
| `frontend-grup-5` | `frontend7vision.yml` | DEV + PROD | EC2 (SCP) | `develop` / `main` |
| `frontend-grup-5` | `desplegament-prod.yml` | PROD | ECS Fargate | `main` |
