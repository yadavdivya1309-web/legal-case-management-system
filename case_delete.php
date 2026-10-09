<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed.'); }
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) { $stmt = $pdo->prepare('DELETE FROM cases WHERE id=?'); $stmt->execute([$id]); set_flash('success', 'Case deleted.'); }
redirect('cases.php');
