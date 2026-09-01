<?php
session_start();
require_once '../config/database.php';
require_once '../config/TenantResolver.php';

// Permitir simular tenant vía ?slug=...
if (!empty($_GET['slug'])) {
  $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['slug']));
  $_SERVER['REQUEST_URI'] = '/' . $slug . '/admin/upload-test.php';
}
// Definir APP_URL si no existe (usado por TenantResolver)
if (!defined('APP_URL')) {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  define('APP_URL', $scheme . '://' . $host . '/catalogo2');
}
TenantResolver::resolve();
require_once '../config/uploads.php';

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = move_uploaded_file_tenant('archivo', 'images', ['jpg','jpeg','png','gif','webp'], 5);
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Test Upload por Tenant</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4">🖼️ Test de Upload por Tenant</h1>
  <p class="text-muted">Tenant actual: <strong><?php echo defined('TENANT_NAME') ? TENANT_NAME : 'Default'; ?></strong> (ID: <?php echo defined('TENANT_ID') ? TENANT_ID : 0; ?>)</p>

  <?php if ($result): ?>
    <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?>">
      <?php echo htmlspecialchars($result['message']); ?>
      <?php if (!empty($result['url'])): ?>
        <div class="mt-2">
          <a href="<?php echo $result['url']; ?>" target="_blank">Ver archivo subido</a>
        </div>
        <div class="small text-muted">Ruta relativa: <?php echo $result['relPath']; ?></div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm bg-white">
    <div class="mb-3">
      <label class="form-label">Seleccione una imagen (JPG, PNG, GIF, WEBP) máx 5MB</label>
      <input type="file" name="archivo" class="form-control" accept="image/*" required>
    </div>
    <button class="btn btn-primary">Subir</button>
  </form>

  <hr class="my-5">
  <div class="card p-3">
    <div><strong>Base dir:</strong> <?php echo tenant_upload_base_dir(defined('TENANT_ID')?TENANT_ID:0); ?></div>
    <div><strong>Base url:</strong> <?php echo tenant_upload_base_url(defined('TENANT_ID')?TENANT_ID:0); ?></div>
  </div>
</div>
</body>
</html>
