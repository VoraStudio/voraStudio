<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=voracms;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== CONTENT TYPES per project/user ===\n";
$stmt = $pdo->query("
    SELECT ct.id, ct.name, ct.slug, ct.base, p.name as project, u.email as user, ct.created_at
    FROM content_types ct
    LEFT JOIN projects p ON ct.project_id = p.id
    LEFT JOIN users u ON ct.user_id = u.id
    WHERE ct.base = 0
    ORDER BY ct.user_id, ct.project_id
");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("CT ID: %d | %s (slug: %s) | Project: %s | User: %s | Created: %s\n",
        $r['id'], $r['name'], $r['slug'], $r['project'] ?? '-', $r['user'] ?? '-', $r['created_at']);
}

echo "\n=== ENTRIES with content type + project info ===\n";
$stmt = $pdo->query("
    SELECT e.id, e.status, ct.name as ct_name, ct.slug as ct_slug, 
           p.name as ct_project, u.email as ct_user,
           e.user_id, e.author_id, au.email as author_email
    FROM entries e
    JOIN content_types ct ON e.content_type_id = ct.id
    LEFT JOIN projects p ON ct.project_id = p.id
    LEFT JOIN users u ON ct.user_id = u.id
    LEFT JOIN users au ON e.author_id = au.id
    ORDER BY e.id
");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("Entry %d | %s | CT: %s (slug: %s) | CT Project: %s | CT User: %s | Entry user_id: %d | Author: %s (%d)\n",
        $r['id'], $r['status'], $r['ct_name'], $r['ct_slug'],
        $r['ct_project'] ?? '-', $r['ct_user'] ?? '-',
        $r['user_id'], $r['author_email'] ?? '-', $r['author_id'] ?? 0);
}

echo "\n=== USERS ===\n";
$stmt = $pdo->query("SELECT id, email, name, company FROM users");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("ID: %d | %s | %s | Company: %s\n", $r['id'], $r['email'], $r['name'], $r['company'] ?? '-');
}

echo "\n=== PROJECTS with user ===\n";
$stmt = $pdo->query("SELECT p.id, p.name, u.email as user_email FROM projects p LEFT JOIN users u ON p.user_id = u.id");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("ID: %d | %s | User: %s\n", $r['id'], $r['name'], $r['user_email'] ?? '-');
}
