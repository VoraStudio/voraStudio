# 05 // Seguridad

## 🔐 JWT (JSON Web Tokens)

La autenticación entre frontend y backend se gestiona mediante **JWT** usando el bundle **LexikJWTAuthenticationBundle**:

1. **Login**: El cliente envía `username` y `password` a `/api/login_check`.
2. **Token**: El servidor valida contra la base de datos y devuelve un token JWT firmado.
3. **Acceso**: El cliente incluye el token en el header `Authorization: Bearer <token>` en cada petición.
4. **Validación**: El backend verifica el token en cada solicitud (modelo **stateless**, escalable).

En el contenedor Docker de producción, las **claves JWT se generan automáticamente** al arrancar vía el entrypoint:

```bash
mkdir -p config/jwt
php bin/console lexik:jwt:generate-keypair --no-interaction
```

---

## 🔒 HTTPS

Todas las comunicaciones deben estar cifradas:

| Entorno | Método | Certificados |
|---------|--------|--------------|
| **DEV** | Let's Encrypt vía Certbot | Automático, renovación periódica |
| **PROD** | AWS Certificate Manager (ACM) | Gestionado por AWS, integrado con ALB |

### En PROD
- El ALB escucha en los puertos 80 y 443.
- El tráfico HTTP (puerto 80) se redirige automáticamente a HTTPS (puerto 443).
- Certificado ACM asociado al dominio DuckDNS y validado vía registro DNS manual.

> ⚠️ **Validación del certificado ACM con DuckDNS**: Como DuckDNS no tiene integración nativa con AWS, la validación del certificado se debe hacer manualmente añadiendo el registro CNAME proporcionado por ACM en la configuración del dominio DuckDNS.

---

## 🌐 CORS (Cross-Origin Resource Sharing)

Como frontend y backend operan en **dominios diferentes** (`grupXX.duckdns.org` vs `api.grupXX.duckdns.org`), hay que configurar CORS en el backend Symfony:

```yaml
# config/packages/nelmio_cors.yaml
nelmio_cors:
    defaults:
        allow_credentials: true
        allow_origin: ['https://grupXX.duckdns.org']
        allow_headers: ['Content-Type', 'Authorization']
        allow_methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']
        max_age: 3600
    paths:
        '^/api/':
            allow_origin: ['https://grupXX.duckdns.org']
            allow_headers: ['Content-Type', 'Authorization']
            allow_methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']
```

---

## 🗝️ Gestión de Secrets

### Principios
- ❌ **No** incluir secrets en el código fuente (ni .env, ni .env.local, ni claves JWT)
- ✅ Utilizar **variables de entorno** para la configuración
- ✅ En producción, utilizar **AWS Secrets Manager** para gestionar información sensible

### Secrets gestionados

| Secret | Dónde se usa |
|--------|-------------|
| `APP_SECRET` | Cifrado de sesiones Symfony |
| `DATABASE_URL` | Conexión a RDS |
| `JWT_SECRET_KEY` / `JWT_PASSPHRASE` | Firma de tokens |
| `AWS_ACCESS_KEY_ID` | Autenticación GitHub → AWS |
| `AWS_SECRET_ACCESS_KEY` | Autenticación GitHub → AWS |

### Inyectados en ECS

Las variables de entorno en los servicios ECS se inyectan vía **ValueFrom** apuntando a los ARNs de Secrets Manager:

```json
{
  "name": "APP_SECRET",
  "valueFrom": "arn:aws:secretsmanager:us-east-1:...:grupXX/symfony/backend:APP_SECRET::"
}
```

### Symfony Secrets

Para información sensible propia de Symfony, se recomienda usar el sistema de **Symfony Secrets** (`bin/console secrets:*`) en entornos cercanos a producción.

---

## 🛡️ Jerarquía de Acceso (Access Control)

El orden de las reglas en `security.yaml` es crítico. Solo la **primera coincidencia** se aplica:

| Prefijo de Ruta | Nivel de Seguridad | Rol Requerido |
|-----------------|---------------------|--------------|
| `/api/login` | Público | `PUBLIC_ACCESS` |
| `/api/` | Privado (API) | `IS_AUTHENTICATED_FULLY` |
| `/usuari`, `/actor`, etc. | Admin (CRUD) | `ROLE_ADMIN` |
| `/` (Dashboard) | Admin | `ROLE_ADMIN` |

---

## 📋 Checklist de Seguridad para el Despliegue

- [ ] JWT configurado y funcional (generación + validación)
- [ ] HTTPS activo en ambos entornos
- [ ] CORS configurado con orígenes explícitos
- [ ] Secrets fuera del repositorio
- [ ] Security groups con mínimo privilegio
- [ ] Base de datos en subred privada
- [ ] No hay credenciales en código fuente
- [ ] `APP_DEBUG=0` en producción
- [ ] `display_errors=off` en producción
