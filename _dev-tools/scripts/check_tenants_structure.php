<?php
include 'config/database.php';
$sql = 'DESCRIBE tenants';
$result = $GLOBALS['conn']->query($sql);
echo "=== ESTRUCTURA TABLA TENANTS ===\n";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | DEFAULT: ' . ($row['Default'] ?? 'NULL') . "\n";
}
?>
