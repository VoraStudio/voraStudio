# 06 // Guía Paso a Paso — Despliegue desde cero

Esta guía detalla los pasos exactos para **reproducir todo el despliegue** sin necesidad de información adicional.

---

## 🏁 Requisitos previos

- Cuenta **AWS Academy** (Learner Lab) con saldo suficiente
- Repositorios de GitHub: `landing-page-grup-5`, `frontend-grup-5`, `backend-grup-5`
- Dominio **DuckDNS** configurado
- **Docker** instalado localmente (para las primeras pruebas)
- **Node.js 20+** y **Composer** (para builds locales)

> ⚠️ **Restricciones AWS Academy (Learner Lab)**: Tenéis un presupuesto limitado. No podéis crear roles IAM nuevos. Usad siempre el rol **LabRole** preexistente. Parad los servicios cuando no se utilicen para no agotar el saldo.

---

## 📦 FASE 1 — Preparación de contenedores

### 1.1 Build y push inicial a ECR

1. Acceded a **AWS Console** → **ECR**
2. Cread dos repositorios:
   - `frontend` (privado)
   - `backend` (privado)
3. Autenticación local:
   ```bash
   aws ecr get-login-password --region <region> | docker login --username AWS --password-stdin <ID_AWS>.dkr.ecr.<region>.amazonaws.com
   ```
4. **Backend**:
   ```bash
   cd backend-grup-5
   docker build -t backend:latest .
   docker tag backend:latest <ID_AWS>.dkr.ecr.<region>.amazonaws.com/backend:latest
   docker push <ID_AWS>.dkr.ecr.<region>.amazonaws.com/backend:latest
   ```
5. **Frontend**:
   ```bash
   cd frontend-grup-5
   docker build -t frontend:latest .
   docker tag frontend:latest <ID_AWS>.dkr.ecr.<region>.amazonaws.com/frontend:latest
   docker push <ID_AWS>.dkr.ecr.<region>.amazonaws.com/frontend:latest
   ```

---

## 🌐 FASE 2 — Red (VPC y Subnets)

1. Cread una **VPC** llamada `grupXX-vpc` con CIDR `10.0.0.0/16`
2. Cread **4 subnets** en 2 AZs diferentes:

   | Subnet | Tipo | CIDR | AZ |
   |--------|------|------|----|
   | `grupXX-subnet-pub-1` | Pública | `10.0.1.0/24` | us-east-1a |
   | `grupXX-subnet-pub-2` | Pública | `10.0.2.0/24` | us-east-1b |
   | `grupXX-subnet-priv-1` | Privada | `10.0.3.0/24` | us-east-1a |
   | `grupXX-subnet-priv-2` | Privada | `10.0.4.0/24` | us-east-1b |

3. Cread un **Internet Gateway** (`grupXX-igw`) y asociadlo a la VPC
4. Configurad la **Route Table** de las subnets públicas: `0.0.0.0/0 → IGW`

---

## 🔐 FASE 3 — Secrets

1. **AWS Console** → **Secrets Manager** → *Store a new secret*
2. Tipo: *Other type of secret* → Plaintext JSON:

   ```json
   {
     "APP_SECRET": "tu_secreto_aleatorio",
     "DATABASE_URL": "mysql://usuario:password@host:3306/7vision",
     "JWT_SECRET_KEY": "...",
     "JWT_PASSPHRASE": "..."
   }
   ```

3. Nombre del secret: `grupXX/symfony/backend`

---

## 🗄️ FASE 4 — Base de datos RDS

1. **AWS Console** → **RDS** → *Create database*
2. Motor: **MySQL**
3. Template: **Free tier** (si el Learner Lab lo permite)
4. Configuración:
   - DB instance identifier: `grupXX-rds`
   - Master username: `admin`
   - Master password: *(generar y guardar)*
   - VPC: `grupXX-vpc`
   - Subnet group: subnets **privadas**
   - Public access: **NO**
   - Security group: `rds-sg` (puerto 3306, origen: `ecs-sg`)
5. Apuntad el **endpoint** para el `DATABASE_URL`

---

## ⚙️ FASE 5 — ECS Cluster

1. **AWS Console** → **ECS** → *Create cluster*
2. Nombre: `grupXX-cluster`
3. Infraestructura: **Fargate only**
4. Crear

---

## 📋 FASE 6 — Task Definitions

### backend-task
1. **ECS** → *Create new Task Definition*
2. Nombre: `backend-task`
3. Tipo: **Fargate**
4. Recursos: 1 vCPU, 3 GB RAM
5. Contenedor:
   - Nombre: `backend-container`
   - Imagen: `<ID_AWS>.dkr.ecr.<region>.amazonaws.com/backend:latest`
   - Puerto: 80
   - **Environment variables → ValueFrom**:
     - `APP_ENV` = `prod`
     - `APP_SECRET` = `arn:aws:secretsmanager:...`
     - `DATABASE_URL` = `arn:aws:secretsmanager:...`
     - `JWT_SECRET_KEY` = `arn:aws:secretsmanager:...`
     - `JWT_PASSPHRASE` = `arn:aws:secretsmanager:...`
6. Rol de ejecución: **LabRole**

### frontend-task
1. Repetid el proceso para `frontend-task`
2. Imagen: `<ID_AWS>.dkr.ecr.<region>.amazonaws.com/frontend:latest`
3. Puerto: 80
4. Rol de ejecución: **LabRole**

---

## 🎯 FASE 7 — Target Groups

1. **EC2** → **Target Groups** → *Create target group*
2. **frontend-tg**: Tipo IP, puerto 80
3. **backend-tg**: Tipo IP, puerto 80

---

## 🛡️ FASE 8 — Security Groups

| SG | Nombre | Reglas de entrada |
|----|--------|-------------------|
| **alb-sg** | Público | HTTP 80 + HTTPS 443 desde `0.0.0.0/0` |
| **ecs-sg** | Privado | Puerto 80 desde `alb-sg` |
| **rds-sg** | Privado | Puerto 3306 desde `ecs-sg` |

---

## ⚖️ FASE 9 — Application Load Balancer

1. **EC2** → **Load Balancers** → *Create ALB*
2. Nombre: `grupXX-alb`
3. Scheme: **internet-facing**
4. VPC: `grupXX-vpc`, subnets **públicas**
5. Security group: `alb-sg`
6. **Listeners**:
   - HTTP 80 (redirige a HTTPS 443)
   - HTTPS 443 (cert ACM)
7. **Routing rules** (en listener HTTPS):
   - `/api/*` → `backend-tg`
   - `/admin*`, `/login*`, `/logout` → `backend-tg`
   - `/css*`, `/js*`, `/lib*` → `backend-tg`
   - `/img/avatars/*`, `/img/posters/*` → `backend-tg`
   - `/*` → `frontend-tg`

---

## 🚀 FASE 10 — ECS Services

1. En el clúster **grupXX-cluster**, id a *Services* → *Create*
2. **backend-service**:
   - Task Definition: `backend-task` (latest)
   - Launch type: **Fargate**
   - Nº de tareas: 1
   - Load balancing: Sí, ALB → `backend-tg`
   - Auto scaling: Target tracking (CPU), 1-4 tareas
3. **frontend-service**:
   - Mismo proceso, apuntando a `frontend-task` y `frontend-tg`

---

## 🌍 FASE 11 — Dominio DuckDNS y HTTPS

1. Apuntad el **DNS name** del ALB (ej: `grupXX-alb-123456.us-east-1.elb.amazonaws.com`)
2. En **DuckDNS**: Cread un registro **A** (o CNAME) apuntando al DNS del ALB
3. En **ACM**: Solicitad un certificado SSL para `*.grupXX.duckdns.org`
4. **Validación DNS manual**: Añadid el registro CNAME que ACM os da en DuckDNS
5. Asociad el certificado al ALB
6. Configurad el listener 443 del ALB con el certificado

---

## 🔄 FASE 12 — GitHub Actions (CI/CD)

### Secrets de GitHub

En cada repositorio, añadid los secrets:

| Secret | Valor |
|--------|-------|
| `AWS_ACCESS_KEY_ID` | (del Learner Lab) |
| `AWS_SECRET_ACCESS_KEY` | (del Learner Lab) |
| `AWS_SESSION_TOKEN` | (del Learner Lab) |
| `AWS_REGION` | `us-east-1` |
| `AWS_ACCOUNT_ID` | (vuestro ID AWS) |
| Secrets EC2 (DEV) | Host, User, SSH Key |

### Verificación

1. Haced un push a `develop` → el workflow DEV debe ejecutarse correctamente
2. Haced un PR de `develop` a `main` → el workflow PROD debe construir y desplegar en ECS

---

## ✅ FASE 13 — Verificación final

- [ ] La **Landing Page** es accesible desde S3
- [ ] El **Frontend** se carga en el dominio DuckDNS
- [ ] La **API** responde en `api.grupXX.duckdns.org/api/...`
- [ ] La **autenticación JWT** funciona (login → token → acceso a rutas protegidas)
- [ ] HTTPS activo (🔒 en el navegador)
- [ ] La **base de datos** es accesible (datos de catálogo se cargan)
- [ ] **CORS** no bloquea peticiones
- [ ] Los **workflows de GitHub Actions** se ejecutan correctamente

---

## 📚 Referencias

- `README.md` — Documentación General de Despliegue (raíz del proyecto)
- `README-back.md` — Documentación backend (raíz del proyecto)
- `README-front.md` — Documentación frontend (raíz del proyecto)
- [Pipeline y Comandos Locales](../pipeline/index.md)
