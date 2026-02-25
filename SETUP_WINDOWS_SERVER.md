# CONFIGURACIÓN PASO A PASO - Windows Server 2019

## 1️⃣ CONFIGURAR XAMPP PUERTO 81

### Archivo: `C:\xampp\apache\conf\httpd.conf`

**Buscar (línea ~52):**
```apache
Listen 80
```

**Cambiar por:**
```apache
Listen 81
```

**Guardar archivo**

### Archivo: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

**Agregar al final:**
```apache
<VirtualHost *:81>
    DocumentRoot "C:/xampp/htdocs/catalogo2"
    ServerName catalogo2.local
    ServerAlias *.catalogo2.local
    
    <Directory "C:/xampp/htdocs/catalogo2">
        Options +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/run/php-fpm.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

---

## 2️⃣ INICIAR SERVICIOS

### Opción A: Usar Panel de Control de XAMPP

1. Abrir: `C:\xampp\xampp-control.exe`
2. Habilitar módulos:
   - Apache (Start)
   - MySQL (Start)
3. Verificar que estén en verde

### Opción B: Usar PowerShell (Como Administrador)

```powershell
# Iniciar Apache
net start Apache2.4

# Iniciar MySQL
net start MySQL

# Verificar estado
Get-Service -DisplayName "*Apache*"
Get-Service -DisplayName "*MySQL*"
```

---

## 3️⃣ VERIFICAR CONECTIVIDAD

### Desde la misma máquina:
```
http://localhost:81/catalogo2
```

### Desde otra máquina (usando IP pública):
```
http://34.193.89.155:81/catalogo2
```

**Resultado esperado:** Página de inicio del catálogo

---

## 4️⃣ CAMBIAR CONTRASEÑA DE MYSQL

### Usar phpMyAdmin (vía XAMPP):

1. Ir a: `http://localhost:81/phpmyadmin`
2. Ingresar sin usuario/contraseña
3. Ir a: **User accounts**
4. Buscar `root` y hacer clic en **Change password**
5. Ingresar nueva contraseña segura
6. Hacer clic en **Go**

### Vía línea de comandos:

```bash
# Abrir CMD o PowerShell
C:\xampp\mysql\bin\mysql -u root

# Una vez en MySQL:
ALTER USER 'root'@'localhost' IDENTIFIED BY 'TuContraseñaSegura123!';
FLUSH PRIVILEGES;
EXIT;
```

**Guardar la nueva contraseña en lugar seguro**

---

## 5️⃣ CONFIGURAR PERMISOS DE CARPETAS

### En PowerShell (Como Administrador):

```powershell
# Ir a la carpeta
cd C:\xampp\htdocs\catalogo2

# Dar permisos de escritura a todos los usuarios
icacls "public\tenants" /grant "Users:(OI)(CI)M" /T
icacls "public\images" /grant "Users:(OI)(CI)M" /T
icacls "logs" /grant "Users:(OI)(CI)M" /T

# Verificar permisos
icacls "public\tenants"
```

**Resultado esperado:** `Users:(OI)(CI)M` (Modify)

---

## 6️⃣ VALIDAR SISTEMA

### Ejecutar Script de Validación:

```bash
cd C:\xampp\htdocs\catalogo2
php scripts/deployment_check.php
```

**Resultado esperado:**
- ✅ 10/10 verificaciones exitosas
- ✅ BD conectada y con tablas
- ✅ Todos los permisos correctos

---

## 7️⃣ ACCEDER A LA APLICACIÓN

### URLs de Acceso:

| Función | URL |
|---------|-----|
| **Super-Admin** | `http://34.193.89.155:81/catalogo2/index.php?controller=superAdmin&action=login` |
| **Tienda Default** | `http://34.193.89.155:81/catalogo2/default` |
| **Admin Default** | `http://34.193.89.155:81/catalogo2/default/index.php?controller=admin` |
| **Tech-Store** | `http://34.193.89.155:81/catalogo2/tech-store` |
| **Admin Tech-Store** | `http://34.193.89.155:81/catalogo2/tech-store/index.php?controller=admin` |

### Credenciales Super-Admin:
```
Usuario: superadmin
Contraseña: SuperAdmin123!
```

---

## 8️⃣ CONFIGURAR FIREWALL (IMPORTANTE)

### Permitir puertos en Windows Firewall:

#### Puerto 81 (Apache):
```powershell
# Como Administrador:
New-NetFirewallRule -DisplayName "Apache HTTP 81" `
  -Direction Inbound -Action Allow -Protocol TCP -LocalPort 81
```

#### Puerto 3306 (MySQL - si acceso remoto):
```powershell
New-NetFirewallRule -DisplayName "MySQL 3306" `
  -Direction Inbound -Action Allow -Protocol TCP -LocalPort 3306
```

#### Verificar reglas:
```powershell
Get-NetFirewallRule -DisplayName "*Apache*" | Select-Object DisplayName, Direction, Action
Get-NetFirewallRule -DisplayName "*MySQL*" | Select-Object DisplayName, Direction, Action
```

---

## 9️⃣ CONFIGURAR HTTPS (OPCIONAL - Recomendado para Producción)

### Generar auto-firmado:

```bash
# En C:\xampp\apache\bin
openssl req -x509 -nodes -days 365 -newkey rsa:2048 `
  -keyout C:\xampp\apache\conf\ssl.key\catalogo2.key `
  -out C:\xampp\apache\conf\ssl.crt\catalogo2.crt
```

### Editar `httpd-ssl.conf`:

```apache
<VirtualHost _default_:8443>
    SSLEngine on
    SSLCertificateFile "C:/xampp/apache/conf/ssl.crt/catalogo2.crt"
    SSLCertificateKeyFile "C:/xampp/apache/conf/ssl.key/catalogo2.key"
    DocumentRoot "C:/xampp/htdocs/catalogo2"
</VirtualHost>
```

### En `httpd.conf`, descomentar:
```apache
LoadModule ssl_module modules/mod_ssl.so
Include conf/extra/httpd-ssl.conf
```

### Reiniciar Apache:
```powershell
net stop Apache2.4
net start Apache2.4
```

---

## 🔟 MONITOREO Y LOGS

### Ver logs de Apache:

```powershell
Get-Content "C:\xampp\apache\logs\error.log" -Tail 50
Get-Content "C:\xampp\apache\logs\access.log" -Tail 50
```

### Ver logs de la aplicación:

```powershell
Get-Content "C:\xampp\htdocs\catalogo2\logs\app.log" -Tail 20
```

### Ver logs de MySQL:

```powershell
Get-Content "C:\xampp\mysql\data\*.err" -Tail 50
```

---

## ⚠️ SOLUCIÓN DE PROBLEMAS

### Apache no inicia

**Error:** "Apache2.4 service failed to start"

**Solución:**
```bash
# Verificar sintaxis
C:\xampp\apache\bin\httpd -t

# Ver error completo
C:\xampp\apache\bin\httpd
```

### MySQL no responde

**Error:** "Access denied for user 'root'"

**Solución:**
```bash
# Resetear contraseña
C:\xampp\mysql\bin\mysql -u root
> ALTER USER 'root'@'localhost' IDENTIFIED BY 'nueva_pass';
```

### Permisos de carpeta incorrectos

**Error:** "Permission denied" en uploads

**Solución:**
```powershell
# Usar icacls
icacls "C:\xampp\htdocs\catalogo2\public\tenants" /grant:r "SYSTEM":(OI)(CI)(F)
icacls "C:\xampp\htdocs\catalogo2\public\tenants" /grant:r "Users":(OI)(CI)(M)
```

### Puerto 81 ya en uso

**Error:** "The specified port is in use"

**Solución:**
```powershell
# Encontrar qué usa el puerto
netstat -ano | findstr ":81"

# Cambiar a otro puerto en httpd.conf (ej: 8081)
```

---

## 📊 CHECKLIST FINAL

```
□ Apache escuchando en puerto 81
□ MySQL ejecutándose
□ Base de datos catalogo_tienda creada
□ Permisos de carpetas configurados
□ Acceso a http://34.193.89.155:81/catalogo2 OK
□ Login de super-admin OK
□ Login de tenant admin OK
□ Cambiar tema/color OK
□ Subida de imágenes OK
□ BD con datos (100+ productos)
□ Logs escribiendo correctamente
□ Firewall configurado (puerto 81)
□ Contraseña MySQL cambiada
□ Script deployment_check.php exitoso
```

---

**Versión:** 1.0  
**Actualizado:** 13 Enero 2025  
**Próximo:** Configurar HTTPS y backups automatizados
