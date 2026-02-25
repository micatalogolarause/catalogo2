# 🔐 CHECKLIST DE SEGURIDAD PRE-PRODUCCIÓN

## 1. SEGURIDAD DE APLICACIÓN

### 1.1 Autenticación y Autorización
- [x] Super-admin requiere contraseña segura (SuperAdmin123!)
- [x] Admin por tenant requiere contraseña (admin123, Tech123!@)
- [x] Roles validados en cada request
- [x] Sesiones con timeout (3600 segundos = 1 hora)
- [x] Permisos validados por controller/action
- [ ] IMPORTANTE: Cambiar todas las contraseñas default ANTES de producción

### 1.2 Acceso a Rutas
- [x] Rutas cliente (/login, /registro, /perfil) deshabilitadas → 404
- [x] Solo super-admin y admin tienen acceso a panel
- [x] TenantResolver valida en cada request
- [x] Tenant inactivo bloquea acceso automático
- [x] Query string no expone lógica sensible

### 1.3 Validación de Datos
- [x] Entrada sanitizada con htmlspecialchars()
- [x] Tipos de dato validados en BD
- [ ] Agregar validación CSRF en formularios (recomendado)
- [ ] Rate limiting en login (recomendado)

---

## 2. SEGURIDAD DE BASE DE DATOS

### 2.1 Credenciales
- [ ] Cambiar contraseña root de MySQL (CRÍTICO)
- [ ] No usar credenciales hardcodeadas en config/database.php en producción
- [ ] Usar variables de entorno o archivo .env externo
- [ ] Contraseña debe ser 12+ caracteres con números, mayúsculas, símbolos

### 2.2 Acceso Remoto
- [ ] MySQL no debe estar disponible en internet
- [ ] Limitar acceso a 127.0.0.1 o red interna solamente
- [ ] Usar firewall para bloquear puerto 3306 desde internet

### 2.3 Backup
- [ ] Configurar backup automático de BD
- [ ] Almacenar backups en ubicación segura (no misma máquina)
- [ ] Probar restauración de backups regularmente

---

## 3. SEGURIDAD DE SERVIDOR WEB

### 3.1 Apache / XAMPP
- [x] .htaccess protege archivos config y sensibles
- [ ] Deshabilitar módulos innecesarios (mod_status, mod_info)
- [ ] Configurar ServerTokens = Prod (no mostrar versión)
- [ ] Configurar ServerSignature Off
- [ ] Habilitar mod_rewrite para URL rewriting
- [ ] AllowOverride = All en directorio /catalogo2

### 3.2 IIS (si se usa)
- [x] web.config configurado con reescritura de URLs
- [x] Headers de seguridad configurados
- [ ] Bloquear acceso a carpeta /config
- [ ] Bloquear acceso a carpeta /logs
- [ ] Configurar Application Pool con identidad limitada

### 3.3 Firewall Windows
- [ ] Puerto 81 abierto solo a IPs autorizadas (si es posible)
- [ ] Puerto 3306 (MySQL) NO abierto a internet
- [ ] Puerto 22 (SSH) solo a admins
- [ ] Evaluar uso de proxy inverso

---

## 4. SEGURIDAD DE ARCHIVOS Y CARPETAS

### 4.1 Permisos de Archivos
- [x] Carpeta /public/tenants con permisos de escritura limitados
- [x] Carpeta /logs con permisos de escritura
- [x] Archivos PHP no directamente ejecutables desde /public
- [ ] Archivo .env (si existe) con permisos 600 (solo lectura propietario)
- [ ] Archivo config/database.php con permisos 644

### 4.2 Archivos Sensibles
- [x] config/database.php no accesible vía HTTP (.htaccess)
- [x] .env no accesible vía HTTP (.htaccess)
- [x] /logs no accesible vía HTTP (.htaccess)
- [ ] Eliminar archivos de backup (.bak, .sql)
- [ ] Eliminar scripts de test/debug en producción

### 4.3 Uploads
- [x] Validación de extensión: jpg, jpeg, png, gif, webp
- [x] Validación de tamaño: máx 5MB
- [x] Archivos guardados fuera del root si es posible
- [x] Per-tenant en /public/tenants/{tenant_id}/
- [ ] Renombrar archivos uploaded con hash único
- [ ] Ejecutar antivirus/malware scan en uploads

---

## 5. SEGURIDAD DE COMUNICACIONES

### 5.1 HTTPS/SSL
- [ ] CERTIFICADO SSL instalado (CRÍTICO para producción)
- [ ] Configurar redireccionamiento HTTP → HTTPS
- [ ] HSTS header configurado (Strict-Transport-Security)
- [ ] Certificado renovable antes de expiración

### 5.2 Headers HTTP
- [x] X-Content-Type-Options: nosniff (evita MIME sniffing)
- [x] X-Frame-Options: SAMEORIGIN (protege contra clickjacking)
- [x] X-XSS-Protection: 1; mode=block (protege XSS)
- [ ] Content-Security-Policy configurado (CSP)
- [ ] Referrer-Policy: strict-origin-when-cross-origin

### 5.3 Cookies de Sesión
- [x] Session cookie con HttpOnly flag (via PHP)
- [ ] Secure flag si HTTPS (recomendado)
- [ ] SameSite=Strict (recomendado)

---

## 6. SEGURIDAD DE CÓDIGO

### 6.1 Errores y Logging
- [ ] Error reporting = OFF en producción (display_errors = Off)
- [x] Logs guardados en carpeta /logs (no accesible públicamente)
- [ ] Logs rotados regularmente (evitar disco lleno)
- [ ] No mostrar stack traces en páginas de error

### 6.2 Sanitización y Validación
- [x] htmlspecialchars() usado en outputs HTML
- [ ] Validación de entrada en formularios
- [ ] Usar prepared statements en BD (verificar)
- [ ] Validar tipos de dato esperados

### 6.3 Dependencias
- [ ] No usar librerías con vulnerabilidades conocidas
- [ ] Auditar terceras librerías incluidas
- [ ] Mantener PHP y MySQL actualizado

---

## 7. SEGURIDAD DE APLICACIÓN

### 7.1 Multi-tenancy
- [x] Tenant resolver en cada request
- [x] Aislamiento de datos por tenant_id
- [x] No exponer IDs internos (usar slug en URLs)
- [x] Verificar que un usuario solo accede a su tenant

### 7.2 Inyecciones
- [x] SQL injection prevención (usar BD con parámetros)
- [ ] XSS injection prevención (htmlspecialchars en outputs)
- [ ] LDAP injection prevención (si usa LDAP)
- [ ] Path traversal prevención (validar rutas de archivos)

### 7.3 Business Logic
- [ ] Validar cambios sensibles (contraseña, email, rol)
- [ ] Auditar cambios en logs
- [ ] Confirmar acciones destructivas
- [ ] Rate limiting en API endpoints

---

## 8. PREPARACIÓN PARA PRODUCCIÓN

### 8.1 Testing
- [ ] Pruebas funcionales completadas
- [ ] Pruebas de seguridad realizadas
- [ ] Pruebas de carga/stress test
- [ ] Compatibilidad navegadores verificada

### 8.2 Monitoreo
- [ ] Sistema de monitoreo configurado (New Relic, etc.)
- [ ] Alertas configuradas para errores/downtimes
- [ ] Logs centralizados (opcional)
- [ ] Métricas de performance monitoreadas

### 8.3 Backup y Recuperación
- [ ] Plan de backup documentado
- [ ] Restauración probada
- [ ] Disaster recovery plan
- [ ] RTO y RPO definidos

### 8.4 Documentación
- [x] DEPLOYMENT_WS2019_IIS.md creado
- [x] SETUP_WINDOWS_SERVER.md creado
- [x] PRODUCTION_READINESS.md creado
- [ ] Runbook de operaciones
- [ ] Matriz de permisos de usuarios
- [ ] Diagrama de arquitectura

---

## 9. POST-DEPLOYMENT

### 9.1 Verificación Inicial
- [ ] Aplicación accesible en http://34.193.89.155:81/catalogo2
- [ ] BD conectada y datos visibles
- [ ] Temas de color funcionando
- [ ] Uploads de imágenes funcionando
- [ ] Logs escribiendo correctamente

### 9.2 Verificación Seguridad
- [ ] No hay errores PHP mostrados en web
- [ ] Archivos config no accesibles vía HTTP
- [ ] HTTPS funcionando (si aplica)
- [ ] Contraseñas default cambiadas
- [ ] Firewall correctamente configurado

### 9.3 Monitoreo Inicial
- [ ] Revisar logs error cada 30 minutos
- [ ] Verificar conectividad cada 1 hora
- [ ] Check de performance cada 4 horas
- [ ] Backup probado al día siguiente

---

## 🚨 CRÍTICOS - HACER ANTES DE LANZAR

1. **Cambiar contraseña MySQL root**
   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'ContraseñaFuerte123!@#';
   ```

2. **Cambiar credenciales default en aplicación**
   - superadmin: Cambiar SuperAdmin123! por contraseña fuerte
   - admin: Cambiar admin123 por contraseña fuerte
   - admin_tech: Cambiar Tech123!@ por contraseña fuerte

3. **Configurar HTTPS**
   - Obtener certificado SSL (Let's Encrypt gratuito)
   - Instalar en Apache/IIS
   - Redirigir HTTP → HTTPS

4. **Configurar backup**
   - Backup automático de BD diario
   - Almacenamiento seguro
   - Prueba de restauración

5. **Habilitar logging**
   - error.log en Apache
   - app.log en aplicación
   - Rotar logs regularmente

6. **Deshabilitar modo debug**
   - Revisar que no haya debug=true en config
   - Error reporting apagado
   - No mostrar versiones de software

---

## ✅ CHECKLIST FINAL

```
AUTENTICACIÓN:
☐ Contraseñas root MySQL cambiadas
☐ Contraseñas admins cambiadas
☐ Sesiones con timeout
☐ Roles validados

BASE DE DATOS:
☐ Acceso remoto deshabilitado
☐ Backup automático configurado
☐ Permisos de DB correctos

SERVIDOR:
☐ Apache/IIS seguro configurado
☐ Firewall apropiadamente configurado
☐ Puertos no necesarios cerrados

ARCHIVOS:
☐ Permisos correctos
☐ Archivos sensibles protegidos
☐ Uploads validados

COMUNICACIONES:
☐ HTTPS configurado (recomendado)
☐ Headers de seguridad
☐ Cookies seguras

CÓDIGO:
☐ Error reporting = OFF
☐ Logs en carpeta privada
☐ Validación de entrada
☐ Sanitización de output

POST-DEPLOYMENT:
☐ Aplicación accessible
☐ BD funcionando
☐ Temas funcionando
☐ Logs monitoreados
☐ Alertas configuradas
☐ Backup probado

```

---

**Completar este checklist ANTES de considerar "en producción"**

**Fecha de revisión:** 13 Enero 2025  
**Próxima auditoría:** 3 meses después del deployment
