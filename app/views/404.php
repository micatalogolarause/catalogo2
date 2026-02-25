<?php
// Vistas de error
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="text-center">
            <h1 class="display-1 fw-bold">404</h1>
            <h2>Página no encontrada</h2>
            <p class="text-muted mb-4">Lo sentimos, la página que buscas no existe.</p>
            <a href="<?php echo APP_URL; ?>" class="btn btn-primary btn-lg">
                <i class="bi bi-house"></i> Volver al Inicio
            </a>
        </div>
    </div>
</body>
</html>
