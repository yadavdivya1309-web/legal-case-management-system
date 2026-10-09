<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$client = ['full_name'=>'', 'email'=>'', 'phone'=>''];
if ($id) { $stmt = $pdo->prepare('SELECT * FROM clients WHERE id = ?'); $stmt->execute([$id]); $client = $stmt->fetch(); if (!$client) { http_response_code(404); exit('Client not found.'); } }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? ''); $email = trim($_POST['email'] ?? ''); $phone = trim($_POST['phone'] ?? '');
    if ($name === '') $errors[] = 'Full name is required.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($email !== '') {
    $checkSql = 'SELECT id FROM clients WHERE email = ?';
    $checkParams = [$email];

    if ($id) {
        $checkSql .= ' AND id != ?';
        $checkParams[] = $id;
    }

    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute($checkParams);

    if ($checkStmt->fetch()) {
        $errors[] = 'This email address is already registered.';
    }
}
    if (mb_strlen($name) > 120) $errors[] = 'Name must be 120 characters or fewer.';
    if ($phone !== '' && !preg_match('/^[0-9]{10}$/', $phone)) {
    $errors[] = 'Phone number must contain exactly 10 digits.';
}
    if (!$errors) {
        if ($id) { $stmt = $pdo->prepare('UPDATE clients SET full_name=?, email=?, phone=? WHERE id=?'); $stmt->execute([$name, $email ?: null, $phone ?: null, $id]); }
        else { $stmt = $pdo->prepare('INSERT INTO clients (full_name,email,phone) VALUES (?,?,?)'); $stmt->execute([$name, $email ?: null, $phone ?: null]); }
        set_flash('success', $id ? 'Client updated successfully.' : 'Client added successfully.'); redirect('clients.php');
    }
    $client = ['full_name'=>$name, 'email'=>$email, 'phone'=>$phone];
}
$pageTitle = $id ? 'Edit client' : 'Add client';
require __DIR__ . '/includes/header.php';
?>
<div class="form-layout"><section class="panel form-panel"><div class="panel-heading"><div><h2>Client information</h2><p class="muted">Enter the contact details below.</p></div></div>
<?php foreach ($errors as $error): ?><div class="alert error"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" class="data-form">
  <label>Full name <span>*</span><input name="full_name" required maxlength="120" value="<?= e($client['full_name']) ?>" placeholder="e.g. Aarav Sharma"></label>
  <label>Email address<input type="email" name="email" maxlength="160" value="<?= e($client['email']) ?>" placeholder="name@example.com"></label>
<label>Phone number
<input
    type="tel"
    name="phone"
    maxlength="10"
    pattern="[0-9]{10}"
    inputmode="numeric"
    value="<?= e($client['phone']) ?>"
    placeholder="Enter 10-digit phone number"
>
</label>sss  <div class="form-actions"><a class="button ghost" href="clients.php">Cancel</a><button class="button primary"><?= $id ? 'Save changes' : 'Add client' ?></button></div>
</form></section><aside class="helper-card"><span class="helper-symbol">♙</span><h3>Client records</h3><p>Keep contact information accurate so cases can be associated with the right client.</p></aside></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
