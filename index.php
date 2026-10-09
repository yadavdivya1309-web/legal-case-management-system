<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Dashboard';

$totalCases = (int)$pdo->query('SELECT COUNT(*) FROM cases')->fetchColumn();
$totalClients = (int)$pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn();
$openCases = (int)$pdo->query("SELECT COUNT(*) FROM cases WHERE status = 'Open'")->fetchColumn();
$inProgress = (int)$pdo->query("SELECT COUNT(*) FROM cases WHERE status = 'In Progress'")->fetchColumn();

$stmt = $pdo->query("SELECT c.id, c.case_number, c.title, cl.full_name AS client_name, c.status, c.next_hearing
                     FROM cases c JOIN clients cl ON cl.id = c.client_id
                     ORDER BY c.created_at DESC LIMIT 6");
$recentCases = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="welcome-row">
  <div><p class="muted">Tuesday, your workspace at a glance</p><h2>Welcome back, Divya 👋</h2><p class="muted">Keep track of client matters and upcoming hearings in one place.</p></div>
  <a class="button primary" href="case_form.php">＋ Add new case</a>
</section>
<?php show_flash(); ?>
<section class="stats-grid">
  <article class="stat-card"><div class="stat-top"><span>Total cases</span><span class="stat-icon blue">▤</span></div><strong><?= $totalCases ?></strong><small>All recorded matters</small></article>
  <article class="stat-card"><div class="stat-top"><span>Active clients</span><span class="stat-icon purple">♙</span></div><strong><?= $totalClients ?></strong><small>Client records</small></article>
  <article class="stat-card"><div class="stat-top"><span>Open cases</span><span class="stat-icon orange">◷</span></div><strong><?= $openCases ?></strong><small>Awaiting next action</small></article>
  <article class="stat-card"><div class="stat-top"><span>In progress</span><span class="stat-icon green">✓</span></div><strong><?= $inProgress ?></strong><small>Currently being handled</small></article>
</section>
<section class="panel">
  <div class="panel-heading"><div><h2>Recent cases</h2><p class="muted">Latest updates in your workspace</p></div><a class="text-link" href="cases.php">View all cases →</a></div>
  <?php if (!$recentCases): ?><div class="empty-state"><h3>No cases yet</h3><p>Add your first case to get started.</p><a class="button primary" href="case_form.php">Add a case</a></div>
  <?php else: ?>
  <div class="table-wrap"><table><thead><tr><th>CASE</th><th>CLIENT</th><th>STATUS</th><th>NEXT HEARING</th><th></th></tr></thead><tbody>
  <?php foreach ($recentCases as $case): ?>
    <tr><td><strong><?= e($case['case_number']) ?></strong><div class="subcell"><?= e($case['title']) ?></div></td><td><?= e($case['client_name']) ?></td><td><span class="status <?= strtolower(str_replace(' ', '-', $case['status'])) ?>"><?= e($case['status']) ?></span></td><td><?= $case['next_hearing'] ? e(date('d M Y', strtotime($case['next_hearing']))) : '—' ?></td><td><a class="row-link" href="case_form.php?id=<?= (int)$case['id'] ?>">Edit</a></td></tr>
  <?php endforeach; ?>
  </tbody></table></div><?php endif; ?>
</section>
<section class="tip-banner"><span class="tip-icon">✦</span><div><strong>Stay organized, one case at a time.</strong><p>Keep case status and hearing dates up to date for a clearer overview.</p></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
