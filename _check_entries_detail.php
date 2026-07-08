<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=voracms;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== TOTS els Content Types de tipus Notícies i Events ===\n";
$stmt = $pdo->query("
    SELECT ct.id, ct.name, ct.slug, ct.base,
           p.name as project_name, u.email as user_email
    FROM content_types ct
    LEFT JOIN projects p ON ct.project_id = p.id
    LEFT JOIN users u ON ct.user_id = u.id
    WHERE ct.name IN ('Notícies', 'Events', 'Noticies')
    ORDER BY ct.project_id, ct.id
");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("CT %d | %s (slug: %s) | base: %d | Projecte: %s | Usuari: %s\n",
        $r['id'], $r['name'], $r['slug'], $r['base'],
        $r['project_name'] ?? '(cap)', $r['user_email'] ?? '-');
}

echo "\n=== Entrades de cada Notícies/Events ===\n";
$stmt = $pdo->query("
    SELECT e.id, e.content_type_id, e.user_id, e.author_id,
           ct.name as ct_name, ct.slug as ct_slug,
           p.name as ct_project, u.email as entry_user,
           au.email as entry_author
    FROM entries e
    JOIN content_types ct ON e.content_type_id = ct.id
    LEFT JOIN projects p ON ct.project_id = p.id
    LEFT JOIN users u ON e.user_id = u.id
    LEFT JOIN users au ON e.author_id = au.id
    WHERE ct.name IN ('Notícies', 'Events', 'Noticies')
    ORDER BY e.content_type_id, e.id
");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("Entry %d | CT %d (%s) | Projecte CT: %s | user_id: %d (%s) | author: %d (%s)\n",
        $r['id'], $r['content_type_id'], $r['ct_name'],
        $r['ct_project'] ?? '-',
        $r['user_id'], $r['entry_user'] ?? '-',
        $r['author_id'], $r['entry_author'] ?? '-');
}
