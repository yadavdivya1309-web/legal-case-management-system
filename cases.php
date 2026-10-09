<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Cases';
$q = trim($_GET['q'] ?? ''); $status = $_GET['status'] ?? '';
$allowedStatuses = ['', 'Open', 'In Progress', 'Closed'];
if (!in_array($status, $allowedStatuses, true)) $status = '';
$sql = "SELECT c.*, cl.full_name AS client_name FROM cases c JOIN clients cl ON cl.id = c.client_id WHERE 1=1";
$params = [];
if ($q !== '') { $sql .= " AND (c.case_number LIKE ? OR c.title LIKE ? OR cl.full_name LIKE ?)"; $like = "%{$q}%"; array_push($params, $like, $like, $like); }
if ($status !== '') { $sql .= " AND c.status = ?"; $params[] = $status; }
$sql .= " ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $cases = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="page-actions"><p class="muted">Track case details, status, and hearing dates.</p><a class="button primary" href="case_form.php">＋ Add case</a></div>
<?php show_flash(); ?>
<section class="panel">
<form class="search-form" method="get"><input name="q" value="<?= e($q) ?>" placeholder="Search case number, title, or client..." aria-label="Search cases"><select name="status" aria-label="Filter by status"><option value="">All statuses</option><?php foreach (['Open','In Progress','Closed'] as $s): ?><option <?= $status === $s ? 'selected' : '' ?> value="<?= e($s) ?>"><?= e($s) ?></option><?php endforeach; ?></select><button class="button secondary">Filter</button><a class="button ghost" href="cases.php">Reset</a></form>
<div class="table-wrap"><table><thead><tr><th>CASE DETAILS</th><th>CLIENT</th><th>TYPE</th><th>STATUS</th><th>NEXT HEARING</th><th>ACTION</th></tr></thead><tbody>
<?php foreach ($cases as $case): ?><tr><td><strong><?= e($case['case_number']) ?></strong><div class="subcell"><?= e($case['title']) ?></div></td><td><?= e($case['client_name']) ?></td><td><?= e($case['case_type']) ?></td><td><span class="status <?= strtolower(str_replace(' ','-',$case['status'])) ?>"><?= e($case['status']) ?></span></td><td><?= $case['next_hearing'] ? e(date('d M Y', strtotime($case['next_hearing']))) : '—' ?></td><td class="actions"><a class="row-link" href="case_form.php?id=<?= (int)$case['id'] ?>">Edit</a><form method="post" action="case_delete.php" data-confirm="Delete this case?"><input type="hidden" name="id" value="<?= (int)$case['id'] ?>"><button class="row-link danger-link">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$cases): ?><tr><td colspan="6" class="empty-cell">No cases match your search.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
