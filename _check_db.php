<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=voracms;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Entries de Projectes VoraStudio ===\n";
$stmt = $pdo->query("SELECT * FROM entries WHERE content_type_id IN (73, 74)");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
}

echo "=== field_values columns ===\n";
$cols = $pdo->query('SHOW COLUMNS FROM field_values');
foreach ($cols as $c) {
    echo "  " . $c['Field'] . " (" . $c['Type'] . ")\n";
}

echo "\n=== Field Values per entry ===\n";
$stmt = $pdo->query('SELECT fv.*, fd.name as field_name FROM field_values fv JOIN field_definitions fd ON fv.field_definition_id = fd.id WHERE fv.entry_id IN (24, 25)');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
}
