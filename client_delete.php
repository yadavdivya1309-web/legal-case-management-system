<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed.'); }
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM cases WHERE client_id = ?'); $stmt->execute([$id]);
    if ((int)$stmt->fetchColumn() === 0) { $stmt = $pdo->prepare('DELETE FROM clients WHERE id = ?'); $stmt->execute([$id]); set_flash('success', 'Client deleted.'); }
    else set_flash('error', 'This client has linked cases and cannot be deleted.');
}
redirect('clients.php');
