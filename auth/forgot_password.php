<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else if (!is_exists($email, 'user', 'email')) {
        $_err['email'] = 'Email not found';
    }

    // Move to step 2 (reset password)
    if (!$_err) {
        $_SESSION['reset_email'] = $email;
        redirect('/auth/reset_password.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Forgot Password';
include '../_head.php';
?>

<p>Enter your registered email address. You will then be able to set a new password.</p>

<form method="post" class="form">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <section>
        <button>Next</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';