# Resumen de cambios (Cuenta de Cobro / numero_cuenta_cobro)

Este repositorio fue actualizado para introducir y mostrar la terminología "Cuenta de Cobro" y para asignar un número por tenant (`numero_cuenta_cobro`). Los cambios principales realizados son:

- Base de datos / datos:
  - Creado y ejecutado: `scripts/migrate_numero_cuenta_cobro.php` (añade y puebla `numero_cuenta_cobro` por tenant).
- Modelos:
  - `app/models/PedidoModel.php` — `crear()` ahora calcula y persiste `numero_cuenta_cobro` por tenant.
- Helpers (reportes / export):
  - `app/helpers/FacturaPDF.php` — ahora muestra "CUENTA DE COBRO" y usa `numero_cuenta_cobro` si existe; nombre de archivo de salida cambiado para incluir el número.
  - `app/helpers/FacturaExcel.php` — idem para Excel.
  - `app/helpers/ReportUtils.php` — nueva utilidad central para reportes (colores/estados).
- Views / UI:
  - `app/views/admin/facturas.php` — textos visibles actualizados a "Cuentas de Cobro".
  - Varias vistas y mensajes en `app/controllers/*` y `app/views/*` actualizados donde era seguro.
- Archivos estáticos generados:
  - `public/invoices/*.html` — 26 archivos actualizados de "Factura" → "Cuenta de Cobro".
- Documentación:
  - `DEPLOYMENT_WINDOWS_SERVER_2019.md`, `CREDENCIALES.md`, `CODE_CLEANUP_REPORT.md`, `REFACTORING_DETAILS.md`, `PLAN_MULTI_TENANCY.md` — menciones visibles actualizadas donde corresponde.
- Scripts de ayuda añadidos:
  - `scripts/replace_facturas_invoices.php` — reemplaza en `public/invoices`.
  - `scripts/replace_facturas_docs.php` — reemplaza en docs (.md/.txt/.html) con heurísticas para evitar cambiar referencias de código.
  - `scripts/test_crear_pedido_cc.php` — prueba para crear pedidos y verificar `numero_cuenta_cobro`.

Notas importantes:
- No se renombraron clases/archivos (`FacturaPDF.php`, etc.) para evitar riesgo de rotura en runtime; sólo se cambiaron textos visibles y comportamiento donde era seguro.
- Se ejecutaron comprobaciones de sintaxis con `php -l` en los archivos modificados.
- Se verificó funcionalmente la creación de pedidos y asignación de `numero_cuenta_cobro` mediante `scripts/test_crear_pedido_cc.php`.

Instrucciones para generar un patch (en tu máquina con `git` instalado):

1. Sitúate en la carpeta del proyecto:

```powershell
cd C:\xampp\htdocs\catalogo2
```

2. Genera un patch con todos los cambios respecto a `HEAD` (o ajusta la referencia):

```powershell
git diff HEAD > changes.patch
```

Alternativamente ejecuta el script proporcionado:

```powershell
scripts\generate_patch.ps1
```

Si necesitas, puedo intentar crear un `changes.patch` aquí, pero el entorno actual no tiene `git` disponible. Por eso he incluido el script PowerShell que generará el patch localmente si ejecutas `git`.

---

¿Quieres que genere ahora el `changes.patch` localmente si puedo instalar git, o prefieres que te entregue directamente un ZIP con los archivos cambiados (copias) para revisar y aplicar manualmente?