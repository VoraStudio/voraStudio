<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=voracms;charset=utf8mb4', 'root', '');
$stmt = $pdo->query('SELECT id, email, api_token, active FROM users');
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("ID: %d | Email: %s | Token: %s | Active: %d\n", $r['id'], $r['email'], $r['api_token'] ?? '(null)', $r['active']);
}
