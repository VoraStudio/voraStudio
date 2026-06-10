# 🚀 Guía de Despliegue — 7Vision

Esta sección recoge toda la documentación relativa al despliegue y la infraestructura del proyecto **7Vision**.

El objetivo es que cualquier persona pueda **reproducir el despliegue completo** desde cero siguiendo esta guía.

---

## 📋 Índice de la guía

| # | Sección | Descripción |
|---|---------|-------------|
| 01 | [Infraestructura AWS](01-infraestructura.md) | Servicios AWS utilizados, red, security groups |
| 02 | [Entorno DEV](02-entorno-dev.md) | Despliegue en desarrollo (EC2, S3) |
| 03 | [Entorno PROD](03-entorno-prod.md) | Despliegue en producción (ECS Fargate, ALB, RDS) |
| 04 | [CI/CD con GitHub Actions](04-cicd.md) | Workflows de automatización y pipelines |
| 05 | [Seguridad](05-seguridad.md) | JWT, HTTPS, CORS, Symfony Secrets |
| 06 | [Guía Paso a Paso](06-paso-a-paso.md) | Pasos exactos para reproducir todo el despliegue |

---

## 📌 Visión General del Proyecto

**7Vision** es una aplicación web de catálogo cinematográfico con arquitectura cliente-servidor:

- **Landing Page** → HTML/CSS/JS estático alojado en **AWS S3**
- **Frontend** → SPA con **Vue 3**, servido vía Apache dentro de un contenedor Docker
- **Backend** → API REST con **Symfony 7**, servido vía Apache + PHP en contenedor Docker
- **Base de datos** → **MySQL** en **AWS RDS**

La aplicación se despliega en **dos entornos** claramente diferenciados:

| Entorno | Propósito | Infraestructura |
|---------|-----------|----------------|
| **DEV** | Integración, pruebas y validación continua | EC2 + S3 (Landing) + RDS compartido |
| **PROD** | Ejecución final en condiciones reales | ECS Fargate + ALB + ECR + RDS independiente |

Ambos entornos comparten la misma base de código pero difieren en configuración, infraestructura y procesos de despliegue, siguiendo las buenas prácticas de **DevOps** y **CI/CD**.
