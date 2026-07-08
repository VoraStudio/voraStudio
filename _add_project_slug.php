<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=voracms;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$ctId = 74; // Projectes VoraStudio (Web)

// 1. Find the max sort_order for current fields
$stmt = $pdo->prepare("SELECT MAX(sort_order) as max_order FROM field_definitions WHERE content_type_id = ?");
$stmt->execute([$ctId]);
$maxOrder = (int)$stmt->fetchColumn();
$newOrder = $maxOrder + 1;

// 2. Insert field_definition for project_slug
$stmt = $pdo->prepare("INSERT INTO field_definitions (name, slug, field_type, required, sort_order, content_type_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute(['Slug del projecte', 'project_slug', 'text', 1, $newOrder, $ctId]);
$fieldDefId = $pdo->lastInsertId();
echo "Field definition created: ID $fieldDefId\n";

// 3. Get all entries of this content type
$stmt = $pdo->prepare("SELECT id FROM entries WHERE content_type_id = ?");
$stmt->execute([$ctId]);
$entries = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Entries found: " . implode(', ', $entries) . "\n";

// 4. Insert field_values for each entry
$slugs = [
    28 => 'aurex',
];

$insertStmt = $pdo->prepare("INSERT INTO field_values (value, entry_id, field_definition_id) VALUES (?, ?, ?)");
foreach ($entries as $entryId) {
    $slug = $slugs[$entryId] ?? null;
    if ($slug) {
        $insertStmt->execute([$slug, $entryId, $fieldDefId]);
        echo "  Entry $entryId → project_slug = '$slug'\n";
    } else {
        echo "  Entry $entryId → NO SLUG DEFINIDO, ponlo a mano!\n";
    }
}

echo "\n¡Hecho!\n";
