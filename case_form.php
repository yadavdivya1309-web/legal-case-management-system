<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$case = ['case_number'=>'', 'title'=>'', 'client_id'=>'', 'case_type'=>'Civil', 'status'=>'Open', 'filing_date'=>date('Y-m-d'), 'next_hearing'=>'', 'notes'=>''];
if ($id) { $stmt = $pdo->prepare('SELECT * FROM cases WHERE id=?'); $stmt->execute([$id]); $case = $stmt->fetch(); if (!$case) { http_response_code(404); exit('Case not found.'); } }
$clients = $pdo->query('SELECT id, full_name FROM clients ORDER BY full_name')->fetchAll();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['case_number','title','client_id','case_type','status','filing_date','next_hearing','notes'] as $key) $case[$key] = trim($_POST[$key] ?? '');
    if ($case['case_number'] === '') $errors[] = 'Case number is required.';
    if ($case['title'] === '') $errors[] = 'Case title is required.';
    if (!filter_var($case['client_id'], FILTER_VALIDATE_INT)) $errors[] = 'Select a client.';
    if (!in_array($case['status'], ['Open','In Progress','Closed'], true)) $errors[] = 'Select a valid status.';
    if (!in_array($case['case_type'], ['Civil','Criminal','Property','Employment','Family','Corporate','Other'], true)) $errors[] = 'Select a valid case type.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $case['filing_date'])) $errors[] = 'Enter a valid filing date.';
    if ($case['next_hearing'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $case['next_hearing'])) $errors[] = 'Enter a valid hearing date.';
    $clientCheck = $pdo->prepare('SELECT COUNT(*) FROM clients WHERE id=?'); $clientCheck->execute([(int)$case['client_id']]);
    if ((int)$clientCheck->fetchColumn() !== 1) $errors[] = 'The selected client does not exist.';
    $dup = $pdo->prepare('SELECT COUNT(*) FROM cases WHERE case_number=? AND id<>?'); $dup->execute([$case['case_number'], $id]);
    if ((int)$dup->fetchColumn() > 0) $errors[] = 'That case number already exists.';
    if (!$errors) {
        $values = [$case['case_number'], $case['title'], (int)$case['client_id'], $case['case_type'], $case['status'], $case['filing_date'], $case['next_hearing'] ?: null, $case['notes'] ?: null];
        if ($id) { $values[] = $id; $stmt = $pdo->prepare('UPDATE cases SET case_number=?, title=?, client_id=?, case_type=?, status=?, filing_date=?, next_hearing=?, notes=? WHERE id=?'); $stmt->execute($values); }
        else { $stmt = $pdo->prepare('INSERT INTO cases (case_number,title,client_id,case_type,status,filing_date,next_hearing,notes) VALUES (?,?,?,?,?,?,?,?)'); $stmt->execute($values); }
        set_flash('success', $id ? 'Case updated successfully.' : 'Case added successfully.'); redirect('cases.php');
    }
}
$pageTitle = $id ? 'Edit case' : 'Add case';
require __DIR__ . '/includes/header.php';
?>
<div class="form-layout"><section class="panel form-panel"><div class="panel-heading"><div><h2>Case information</h2><p class="muted">Fields marked * are required.</p></div></div>
<?php foreach ($errors as $error): ?><div class="alert error"><?= e($error) ?></div><?php endforeach; ?>
<?php if (!$clients): ?><div class="alert error">Add a client before creating a case. <a href="client_form.php">Add client</a></div><?php else: ?>
<form method="post" class="data-form two-col">
  <label>Case number <span>*</span><input name="case_number" required maxlength="40" value="<?= e($case['case_number']) ?>" placeholder="e.g. LC-2026-003"></label>
  <label>Case title <span>*</span><input name="title" required maxlength="180" value="<?= e($case['title']) ?>" placeholder="Short case title"></label>
  <label>Client <span>*</span><select name="client_id" required><option value="">Select a client</option><?php foreach ($clients as $client): ?><option value="<?= (int)$client['id'] ?>" <?= (string)$case['client_id'] === (string)$client['id'] ? 'selected' : '' ?>><?= e($client['full_name']) ?></option><?php endforeach; ?></select></label>
  <label>Case type <span>*</span><select name="case_type"><?php foreach (['Civil','Criminal','Property','Employment','Family','Corporate','Other'] as $type): ?><option <?= $case['case_type'] === $type ? 'selected' : '' ?> value="<?= e($type) ?>"><?= e($type) ?></option><?php endforeach; ?></select></label>
  <label>Status <span>*</span><select name="status"><?php foreach (['Open','In Progress','Closed'] as $s): ?><option <?= $case['status'] === $s ? 'selected' : '' ?> value="<?= e($s) ?>"><?= e($s) ?></option><?php endforeach; ?></select></label>
  <label>Filing date <span>*</span><input type="date" name="filing_date" required value="<?= e($case['filing_date']) ?>"></label>
  <label>Next hearing date<input type="date" name="next_hearing" value="<?= e($case['next_hearing']) ?>"></label>
  <label class="full-width">Case notes<textarea name="notes" rows="4" maxlength="5000" placeholder="Add a brief note..."><?= e($case['notes']) ?></textarea></label>
  <div class="form-actions full-width"><a class="button ghost" href="cases.php">Cancel</a><button class="button primary"><?= $id ? 'Save changes' : 'Create case' ?></button></div>
</form><?php endif; ?></section><aside class="helper-card"><span class="helper-symbol">▤</span><h3>Case tracking</h3><p>Use a unique case number and keep status and hearing dates current. Sample data only.</p><div class="mini-rule"></div><small>TIP</small><p>Try searching for a case after saving it to test the filter.</p></aside></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
