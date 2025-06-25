<?php
    $dsn = 'mysql:host=your_host;dbname=your_db;charset=utf8';
    $username = 'your_username';
    $password = 'your_password';
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
?>