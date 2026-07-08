<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=voracms;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Entries de Projectes VoraStudio ===\n";
$stmt = $pdo->query("SELECT id, content_type_id, status FROM entries WHERE content_type_id = 74");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("Entry %d | CT: %d | Status: %s\n", $r['id'], $r['content_type_id'], $r['status']);
}

echo "\n=== FieldValues d'aquestes entries ===\n";
$stmt = $pdo->query("SELECT fv.entry_id, fd.name, fv.value FROM field_values fv JOIN field_definitions fd ON fv.field_definition_id = fd.id WHERE fv.entry_id IN (SELECT id FROM entries WHERE content_type_id = 74)");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("Entry %d | %s: %s\n", $r['entry_id'], $r['name'], mb_substr($r['value'], 0, 60));
}
