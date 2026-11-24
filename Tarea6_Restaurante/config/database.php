<?php
declare(strict_types=1);

/*
 * CONEXIÓN A LA BASE DE DATOS
 * Hosting: InfinityFree
 */

$host = 'sql204.infinityfree.com';
$dbname = 'if0_43517302_restaurante';
$username = 'if0_43517302';
$password = 'Mateolima2020';

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

} catch (PDOException $exception) {
    error_log('Error de conexión MySQL: ' . $exception->getMessage());

    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Verifique la configuración.');
}