<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=voracms;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Entry 26
echo "=== Entry 26 full ===\n";
$stmt = $pdo->query("SELECT * FROM entries WHERE id = 26");
print_r($stmt->fetch(PDO::FETCH_ASSOC));

echo "\n=== Content Type of entry 26 ===\n";
$stmt = $pdo->query("SELECT ct.*, p.name as project_name, u.email as user_email FROM content_types ct LEFT JOIN projects p ON ct.project_id = p.id LEFT JOIN users u ON ct.user_id = u.id WHERE ct.id = (SELECT content_type_id FROM entries WHERE id = 26)");
print_r($stmt->fetch(PDO::FETCH_ASSOC));

echo "\n=== All entries from Notícies Victoria Taylor ===\n";
$stmt = $pdo->query("
    SELECT e.*, u.email as user_email 
    FROM entries e 
    LEFT JOIN users u ON e.user_id = u.id 
    WHERE e.content_type_id = 29
");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($r);
}

echo "\n=== All entries from Events Victoria Taylor ===\n";
$stmt = $pdo->query("
    SELECT e.*, u.email as user_email 
    FROM entries e 
    LEFT JOIN users u ON e.user_id = u.id 
    WHERE e.content_type_id = 28
");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($r);
}
