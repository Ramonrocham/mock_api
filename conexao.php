<?php
require __DIR__ . '/vendor/autoload.php';
/**
 * Conexão com o banco de dados
 * Configure seu banco de dados no ".env Exemplo" e renomeie para ".env"
 * 
 */

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getDbConnection(): mysqli {

    // --- Configurações do Banco de Dados ---
    // Ajuste conforme necessário para seu ambiente.
    $servername = $_ENV['DB_HOST'];
    $username =  $_ENV['DB_USER'];
    $password =  $_ENV['DB_PASSWORD'];
    $port =  $_ENV['DB_PORT'];
    $dbname =  $_ENV['DB_NAME'];

    try {
        $temp_conn = new mysqli($servername, $username, $password, '', $port);

        $temp_conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $temp_conn->close();

        $conn = new mysqli($servername, $username, $password, $dbname, $port);
        $conn->set_charset("utf8mb4");

        initializeDatabase($conn);
        return $conn;

    } catch (mysqli_sql_exception $e) {
        error_log("Falha na inicialização do DB: " . $e->getMessage());
        throw new mysqli_sql_exception("Falha ao inicializar o serviço de banco de dados.", $e->getCode(), $e);
    }
}

function initializeDatabase(mysqli $conn) {
    $sqlUser = "
    CREATE TABLE IF NOT EXISTS `users` (
      `id` varchar(255) NOT NULL,
      `username` varchar(255) NOT NULL,
      `password` varchar(255) NOT NULL,
      `name` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `number` varchar(50) DEFAULT NULL,
      `status` varchar(20) NOT NULL DEFAULT 'active',
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `username_unique` (`username`),
      UNIQUE KEY `email_unique` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $sqlLogLogin = "
    CREATE TABLE IF NOT EXISTS `log_login` (
      `log_id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` varchar(255) DEFAULT NULL,
      `username` varchar(255) NOT NULL,
      `timestamp` datetime NOT NULL,
      `status` varchar(20) NOT NULL,
      PRIMARY KEY (`log_id`),
      KEY `fk_log_login_user_id` (`user_id`),
      CONSTRAINT `fk_log_login_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $sqlLogChanges = "
    CREATE TABLE IF NOT EXISTS `log_changes` (
      `log_id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` varchar(255) DEFAULT NULL,
      `user_data` json NOT NULL,
      `timestamp` datetime NOT NULL,
      `action` varchar(50) NOT NULL,
      `status` varchar(20) NOT NULL,
      PRIMARY KEY (`log_id`),
      KEY `fk_log_changes_user_id` (`user_id`),
      CONSTRAINT `fk_log_changes_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    // Executa as queries e lança uma exceção em caso de erro
    if (!$conn->query($sqlUser)) { throw new Exception("Erro ao criar tabela 'users': " . $conn->error); }
    if (!$conn->query($sqlLogLogin)) { throw new Exception("Erro ao criar tabela 'log_login': " . $conn->error); }
    if (!$conn->query($sqlLogChanges)) { throw new Exception("Erro ao criar tabela 'log_changes': " . $conn->error); }
}