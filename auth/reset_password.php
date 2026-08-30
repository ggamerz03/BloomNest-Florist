<?php
include '../_base.php';

// ----------------------------------------------------------------------------

// Must come from forgot_password.php with a verified email in session
$reset_email = $_SESSION['reset_email'] ?? null;

if (!$reset_email) {
    redirect('/auth/forgot_password.php');
}

if (is_post()) {
    $new_password = req('new_password');
    $confirm      = req('confirm');

    // Validate: new_password
    if ($new_password == '') {
        $_err['new_password'] = 'Required';
    }
    else if (strlen($new_password) < 5 || strlen($new_password) > 100) {
        $_err['new_password'] = 'Between 5-100 characters';
    }

    // Validate: confirm
    if (!$confirm) {
        $_err['confirm'] = 'Required';
    }
    else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5-100 characters';
    }
    else if ($confirm != $new_password) {
        $_err['confirm'] = 'Not matched';
    }

    // DB operation
    if (!$_err) {
        $stm = $_db->prepare('UPDATE user SET password = SHA(?) WHERE email = ?');
        $stm->execute([$new_password, $reset_email]);

        unset($_SESSION['reset_email']);

        temp('info', 'Password reset successfully. Please login.');
        redirect('/auth/login.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Reset Password';
include '../_head.php';
?>

<p>Set a new password for <b><?= encode($reset_email) ?></b>.</p>

<form method="post" class="form">
    <label for="new_password">New Password</label>
    <?= html_password('new_password', 'maxlength="100"') ?>
    <?= err('new_password') ?>

    <label for="confirm">Confirm Password</label>
    <?= html_password('confirm', 'maxlength="100"') ?>
    <?= err('confirm') ?>

    <section>
        <button type="button" data-get="/auth/login.php"">Cancel</button>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';