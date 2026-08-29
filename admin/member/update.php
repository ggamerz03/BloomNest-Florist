<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
    $stm->execute([$id]);
    $u = $stm->fetch();

    if (!$u) {
        redirect('list.php');
    }

    extract((array)$u);
    $_SESSION['profile_photo'] = $u->profile_photo;
}

if (is_post()) {
    $id    = req('id');
    $name  = req('name');
    $phone = req('phone');
    $role  = req('role');
    $profile_photo = $_SESSION['profile_photo'];
    $f = get_file('profile_photo');

    // Validate: name
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    // Validate: phone
    if ($phone == '') {
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

    // Validate: profile_photo (file) --> optional
    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['profile_photo'] = 'Must be image';
        }
        else if ($f->size > 1 * 1024 * 1024) {
            $_err['profile_photo'] = 'Maximum 1MB';
        }
    }

    // DB operation
    if (!$_err) {
        // (1) Delete old photo and save new one --> optional
        if ($f) {
            if ($profile_photo && file_exists("../../photos/$profile_photo")) {
                unlink("../../photos/$profile_photo");
            }
            $profile_photo = save_photo($f, '../../photos');
        }

        // (2) Update user
        $stm = $_db->prepare('
            UPDATE user
            SET name = ?, phone = ?, role = ?, profile_photo = ?
            WHERE id = ?
        ');
        $stm->execute([$name, $phone, $role, $profile_photo, $id]);

        temp('info', 'Record updated');
        redirect('list.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Member | Update';
include '../../_head.php';
?>

<p>
    <button data-get="list.php">Back to List</button>
</p>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="id">Id</label>
    <b><?= $id ?></b>
    <span></span>

    <label for="email">Email</label>
    <?= html_text('email', 'readonly disabled') ?>
    <span></span>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="phone">Phone No.</label>
    <?= html_text('phone', 'maxlength="100"') ?>
    <?= err('phone') ?>

    <label for="role">Role</label>
    <?= html_select('role', ['Admin' => 'Admin', 'Member' => 'Member'], null) ?>
    <?= err('role') ?>

    <label for="profile_photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('profile_photo', 'image/*', 'hidden') ?>
        <img src="/photos/<?= $profile_photo ?>">
    </label>
    <?= err('profile_photo') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../_foot.php';