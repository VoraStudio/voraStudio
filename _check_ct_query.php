<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=voracms;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Simulate findAllForAdmin with project_id = 20 (Web)
$projectId = 20;
$stmt = $pdo->prepare("
    SELECT ct.id, ct.name, ct.slug, ct.base, ct.project_id, ct.active
    FROM content_types ct
    WHERE (ct.project_id = :project OR (ct.base = 1 AND ct.project_id IS NULL))
    ORDER BY ct.base DESC, ct.name ASC
");
$stmt->execute(['project' => $projectId]);
echo "=== findAllForAdmin (project_id=20) ===\n";
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("CT %d | %s | slug: %s | base: %d | project_id: %s | active: %d\n",
        $r['id'], $r['name'], $r['slug'], $r['base'], $r['project_id'] ?? 'NULL', $r['active']);
}

echo "\n=== ALL content types ===\n";
$stmt2 = $pdo->query("SELECT id, name, slug, base, project_id, active, user_id FROM content_types ORDER BY id");
while ($r = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    printf("CT %d | %s | slug: %s | base: %d | project: %s | user: %d | active: %d\n",
        $r['id'], $r['name'], $r['slug'], $r['base'], $r['project_id'] ?? 'NULL', $r['user_id'], $r['active']);
}
