<?php

//Configurações do Banco de Dados
$host = 'localhost';
$db   = 'proj_rastreamento_carnes'; 
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4'; 

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opções de PDO: Garante que erros de SQL gerem exceções PHP
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mostra erros de forma clara
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna resultados como array associativo (coluna => valor)
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desativa a emulação para segurança (previne SQL injection)
];

try {
     // Instancia a conexão PDO
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Se a conexão falhar, interrompe o script e exibe a mensagem de erro.
     die("Erro de Conexão: " . $e->getMessage());
}
?>