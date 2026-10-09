<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Clients';
$q = trim($_GET['q'] ?? '');
$sql = "SELECT cl.*, COUNT(c.id) AS case_count FROM clients cl LEFT JOIN cases c ON c.client_id = cl.id";
$params = [];
if ($q !== '') { $sql .= " WHERE cl.full_name LIKE ? OR cl.email LIKE ? OR cl.phone LIKE ?"; $like = "%{$q}%"; $params = [$like, $like, $like]; }
$sql .= " GROUP BY cl.id ORDER BY cl.created_at DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $clients = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="page-actions"><p class="muted">Manage contact details and client records.</p><a class="button primary" href="client_form.php">＋ Add client</a></div>
<?php show_flash(); ?>
<section class="panel">
  <form class="search-form" method="get"><input name="q" value="<?= e($q) ?>" placeholder="Search name, email, or phone..." aria-label="Search clients"><button class="button secondary">Search</button><?php if ($q !== ''): ?><a class="button ghost" href="clients.php">Clear</a><?php endif; ?></form>
  <div class="table-wrap"><table><thead><tr><th>CLIENT</th><th>EMAIL</th><th>PHONE</th><th>CASES</th><th>ACTIONS</th></tr></thead><tbody>
  <?php foreach ($clients as $client): ?><tr><td><strong><?= e($client['full_name']) ?></strong></td><td><?= e($client['email'] ?: '—') ?></td><td><?= e($client['phone'] ?: '—') ?></td><td><?= (int)$client['case_count'] ?></td><td class="actions"><a class="row-link" href="client_form.php?id=<?= (int)$client['id'] ?>">Edit</a><?php if ((int)$client['case_count'] === 0): ?><form method="post" action="client_delete.php" data-confirm="Delete this client?"><input type="hidden" name="id" value="<?= (int)$client['id'] ?>"><button class="row-link danger-link">Delete</button></form><?php endif; ?></td></tr><?php endforeach; ?>
  <?php if (!$clients): ?><tr><td colspan="5" class="empty-cell">No clients found.</td></tr><?php endif; ?>
  </tbody></table></div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
