<?php

echo "=== Prueba de Conexión a Base de Datos MySQL ===\n";

// Configurar la conexión
$host = '127.0.0.1';
$database = 'desarrollo_software_1';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexión exitosa a MySQL\n";
    echo "Base de datos: $database\n";
    
    // Verificar tablas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\n=== Tablas en la base de datos ===\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    // Verificar usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\n=== Usuarios en la tabla ===\n";
    echo "Total de usuarios: " . $result['total'] . "\n";
    
    if ($result['total'] > 0) {
        $stmt = $pdo->query("SELECT id, name, email, created_at FROM users LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($users as $user) {
            echo "- ID: {$user['id']}, Nombre: {$user['name']}, Email: {$user['email']}, Creado: {$user['created_at']}\n";
        }
    }
    // Verificar proyectos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM proyectos");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\n=== Proyectos en la tabla ===\n";
    echo "Total de proyectos: " . $result['total'] . "\n";
    
    if ($result['total'] > 0) {
        $stmt = $pdo->query("SELECT p.id, p.nombre, p.estado, p.monto, p.responsable, p.created_by, u.name as creador_nombre FROM proyectos p LEFT JOIN users u ON p.created_by = u.id LIMIT 5");
        $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($proyectos as $proyecto) {
            echo "- ID: {$proyecto['id']}, Nombre: {$proyecto['nombre']}\n";
            echo "  Estado: {$proyecto['estado']}, Monto: \${$proyecto['monto']}\n";
            echo "  Responsable: {$proyecto['responsable']}\n";
            echo "  Creado por: {$proyecto['creador_nombre']} (ID: {$proyecto['created_by']})\n\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
}
