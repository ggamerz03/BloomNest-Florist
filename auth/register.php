<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');
    $name     = req('name');
    $phone    = req('phone');
    $f = get_file('profile_photo');

    // Validate: email
    if (!$email) {
        $_err['email'] = 'Required';
    }
    else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else if (!is_unique($email, 'user', 'email')) {
        $_err['email'] = 'Duplicated';
    }

    // Validate: password
    if (!$password) {
        $_err['password'] = 'Required';
    }
    else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Between 5-100 characters';
    }

    // Validate: confirm
    if (!$confirm) {
        $_err['confirm'] = 'Required';
    }
    else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5-100 characters';
    }
    else if ($confirm != $password) {
        $_err['confirm'] = 'Not matched';
    }

    // Validate: name
    if (!$name) {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    // Validate: phone
    if (!$phone) {
        $_err['phone'] = 'Required';
    }
    else if (strlen($phone) > 100) {
        $_err['phone'] = 'Maximum 100 characters';
    }
    else if (!is_phone($phone)) {
        $_err['phone'] = 'Invalid phone number';
    }

    // Validate: profile_photo (file)
    if (!$f) {
        $_err['profile_photo'] = 'Required';
    }
    else if (!str_starts_with($f->type, 'image/')) {
        $_err['profile_photo'] = 'Must be image';
    }
    else if ($f->size > 1 * 1024 * 1024) {
        $_err['profile_photo'] = 'Maximum 1MB';
    }

    // DB operation
    if (!$_err) {
        // (1) Save photo
        $profile_photo = save_photo($f, '../photos');
        
        // (2) Insert user (member)
        $stm = $_db->prepare('
            INSERT INTO user (email, password, name, phone, profile_photo, role)
            VALUES (?, SHA1(?), ?, ?, ?, "Member")
        ');
        $stm->execute([$email, $password, $name, $phone, $profile_photo]);

        temp('info', 'Registration successful. Please login.');
        redirect('/auth/login.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Register';
include '../_head.php';
?>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <label for="password">Password</label>
    <?= html_password('password', 'maxlength="100"') ?>
    <?= err('password') ?>

    <label for="confirm">Confirm</label>
    <?= html_password('confirm', 'maxlength="100"') ?>
    <?= err('confirm') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="phone">Phone No.</label>
    <?= html_text('phone', 'maxlength="100"') ?>
    <?= err('phone') ?>

    <label for="profile_photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('profile_photo', 'image/*', 'hidden') ?>
        <img src="/images/default_photo.jpg">
    </label>
    <?= err('profile_photo') ?>

    <section>
        <button type="button" data-get="/cart/product_list.php">Back to Home</button>
        <button>Register</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';