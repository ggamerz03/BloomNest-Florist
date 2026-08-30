<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

if (is_post()) {
    $email = req('email');
    $name  = req('name');
    $phone = req('phone');
    $role  = req('role');
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

    // Validate: role
    if (!in_array($role, ['Admin', 'Member'])) {
        $_err['role'] = 'Required';
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
        $profile_photo = save_photo($f, '../../photos');

        // (2) Insert user with a default password (123456, hashed)
        $stm = $_db->prepare('
            INSERT INTO user (email, password, name, phone, profile_photo, role)
            VALUES (?, SHA1(?), ?, ?, ?, ?)
        ');
        $stm->execute([$email, SHA1('123456'), $name, $phone, $profile_photo, $role]);

        temp('info', 'Record inserted. Default password: 123456');
        redirect('list.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Insert';
include '../../_head.php';
?>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="phone">Phone No.</label>
    <?= html_text('phone', 'maxlength="100"') ?>
    <?= err('phone') ?>

    <label for="role">Role</label>
    <?= html_select('role', ['Member' => 'Member', 'Admin' => 'Admin'], null) ?>
    <?= err('role') ?>

    <label for="profile_photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('profile_photo', 'image/*', 'hidden') ?>
        <img src="/images/default_photo.jpg">
    </label>
    <?= err('profile_photo') ?>

    <section>
        <p>Default password: <b>123456</b> (the user can change it after logging in)</p>
        <button data-get="list.php">Back to List</button>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../_foot.php';