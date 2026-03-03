<?php
/**
 * Tenant Admin Controller
 * Gestiona creaciÃ³n y provisioning de nuevos tenants
 * MIGRADO: usa helper functions PDO-compatibles (sin mysqli directo)
 */

class TenantAdminController {

    /**
     * Crear nuevo tenant con provisioning automÃ¡tico
     */
    public function crearTenant($data) {
        global $pdo;

        // Validaciones
        $validacion = $this->validarDatosTenant($data);
        if (!$validacion['valid']) {
            return ['success' => false, 'message' => $validacion['error']];
        }

        try {
            $pdo->beginTransaction();

            // 1. Crear tenant
            $logo   = $data['logo']   ?? NULL;
            $tema   = $data['tema']   ?? 'claro';
            $estado = $data['estado'] ?? 'activo';

            $sql = "INSERT INTO tenants (nombre, slug, whatsapp_phone, logo, tema, estado) VALUES (?, ?, ?, ?, ?, ?)";
            ejecutarConsulta($sql, 'ssssss', [$data['nombre'], $data['slug'], $data['whatsapp_phone'], $logo, $tema, $estado]);
            $tenant_id = obtenerUltimoId();

            // 2. Provisioning automÃ¡tico
            $provisioning = $this->provisioning($tenant_id, $data);
            if (!$provisioning['success']) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Error en provisioning: ' . $provisioning['message']];
            }

            // 3. Crear carpetas de uploads por tenant
            if (!function_exists('ensure_upload_dirs_for_tenant')) {
                require_once dirname(__DIR__, 2) . '/config/uploads.php';
            }
            ensure_upload_dirs_for_tenant($tenant_id);

            $pdo->commit();

            return [
                'success'   => true,
                'message'   => 'Tenant creado exitosamente',
                'tenant_id' => $tenant_id,
                'slug'      => $data['slug'],
                'url'       => "http://localhost/catalogo2/{$data['slug']}"
            ];

        } catch (Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Provisioning automÃ¡tico de datos iniciales para nuevo tenant
     * NOTA: solo se crea el usuario admin; cada tenant gestiona sus propias categorÃ­as
     */
    private function provisioning($tenant_id, $data) {
        try {
            if (!empty($data['admin_email']) && !empty($data['admin_usuario'])) {
                $password_hash = hash('sha256', $data['admin_password'] ?? 'admin123');
                $sql = "INSERT INTO usuarios (tenant_id, usuario, email, password, nombre, rol, activo) VALUES (?, ?, ?, ?, ?, 'admin', 1)";
                ejecutarConsulta($sql, 'issss', [$tenant_id, $data['admin_usuario'], $data['admin_email'], $password_hash, $data['nombre']]);
            }

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Validar datos del tenant
     */
    private function validarDatosTenant($data) {
        if (empty($data['nombre'])) {
            return ['valid' => false, 'error' => 'El nombre del tenant es requerido'];
        }

        if (empty($data['slug'])) {
            return ['valid' => false, 'error' => 'El slug es requerido'];
        }

        if (!preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            return ['valid' => false, 'error' => 'El slug solo puede contener letras minÃºsculas, nÃºmeros y guiones'];
        }

        $sql = "SELECT id FROM tenants WHERE slug = ?";
        if (obtenerFila($sql, 's', [$data['slug']])) {
            return ['valid' => false, 'error' => 'El slug ya existe'];
        }

        if (empty($data['whatsapp_phone'])) {
            return ['valid' => false, 'error' => 'El nÃºmero de WhatsApp es requerido'];
        }

        return ['valid' => true];
    }

    /**
     * Obtener todos los tenants
     */
    public function obtenerTenants($filtro = []) {
        $sql = "SELECT id, nombre, slug, whatsapp_phone, estado, created_at FROM tenants";
        $params = [];

        if (!empty($filtro['estado'])) {
            $sql .= " WHERE estado = ?";
            $params[] = $filtro['estado'];
        }

        $sql .= " ORDER BY created_at DESC";

        return obtenerFilas($sql, 's', $params);
    }

    /**
     * Obtener tenant por ID
     */
    public function obtenerTenant($tenant_id) {
        $sql = "SELECT * FROM tenants WHERE id = ?";
        return obtenerFila($sql, 'i', [$tenant_id]);
    }

    /**
     * Actualizar tenant
     */
    public function actualizarTenant($tenant_id, $data) {
        $campos = [];
        $params = [];

        if (!empty($data['nombre'])) {
            $campos[] = "nombre = ?";
            $params[] = $data['nombre'];
        }

        if (!empty($data['whatsapp_phone'])) {
            $campos[] = "whatsapp_phone = ?";
            $params[] = $data['whatsapp_phone'];
        }

        if (!empty($data['tema'])) {
            $campos[] = "tema = ?";
            $params[] = $data['tema'];
        }

        if (!empty($data['estado'])) {
            $campos[] = "estado = ?";
            $params[] = $data['estado'];
        }

        if (empty($campos)) {
            return ['success' => false, 'message' => 'No hay datos para actualizar'];
        }

        $campos[] = "updated_at = NOW()";
        $params[] = $tenant_id;

        $sql = "UPDATE tenants SET " . implode(", ", $campos) . " WHERE id = ?";
        ejecutarConsulta($sql, '', $params);

        return ['success' => true, 'message' => 'Tenant actualizado'];
    }

    /**
     * Obtener estadÃ­sticas de tenant
     */
    public function obtenerEstadisticas($tenant_id) {
        $stats = [];

        foreach (['productos', 'clientes', 'pedidos', 'categorias'] as $tabla) {
            $row = obtenerFila("SELECT COUNT(*) as total FROM {$tabla} WHERE tenant_id = ?", 'i', [$tenant_id]);
            $stats[$tabla] = $row['total'] ?? 0;
        }

        $row = obtenerFila("SELECT SUM(total) as total FROM pedidos WHERE tenant_id = ?", 'i', [$tenant_id]);
        $stats['ventas_total'] = $row['total'] ?? 0;

        return $stats;
    }
}
