<?php
include '../_base.php';

// ----------------------------------------------------------------------------

// Authenticated users
auth();

$is_member = $_user->role == 'Member';

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
    $stm->execute([$_user->id]);
    $u = $stm->fetch();

    if (!$u) {
        redirect('/');
    }

    extract((array)$u);
    $_SESSION['profile_photo'] = $u->profile_photo;
}

if (is_post()) {
    $name          = req('name');
    $phone         = req('phone');
    $profile_photo = $_SESSION['profile_photo'];
    $f = get_file('profile_photo');

    if ($is_member) {
        $address_line = req('address_line');
        $city         = req('city');
        $state        = req('state');
        $postcode     = req('postcode');
    }

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

    // Validate: profile_photo (file) --> optional
    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['profile_photo'] = 'Must be image';
        }
        else if ($f->size > 1 * 1024 * 1024) {
            $_err['profile_photo'] = 'Maximum 1MB';
        }
    }

    // Validate: address (Members only)
    if ($is_member) {
        if ($address_line == '') {
            $_err['address_line'] = 'Required';
        }
        else if (strlen($address_line) > 255) {
            $_err['address_line'] = 'Maximum 255 characters';
        }

        if ($city == '') {
            $_err['city'] = 'Required';
        }
        else if (strlen($city) > 100) {
            $_err['city'] = 'Maximum 100 characters';
        }

        if ($state == '') {
            $_err['state'] = 'Required';
        }
        else if (strlen($state) > 100) {
            $_err['state'] = 'Maximum 100 characters';
        }

        if ($postcode == '') {
            $_err['postcode'] = 'Required';
        }
        else if (!preg_match('/^\d{5}$/', $postcode)) {
            $_err['postcode'] = 'Invalid postcode';
        }
    }

    // DB operation
    if (!$_err) {
        // (1) Delete old photo and save new one --> optional
        if ($f) {
            if ($profile_photo && file_exists("../photos/$profile_photo")) {
                unlink("../photos/$profile_photo");
            }
            $profile_photo = save_photo($f, '../photos');
        }

        // (2) Update user
        if ($is_member) {
            $stm = $_db->prepare('
                UPDATE user
                SET name = ?, phone = ?, profile_photo = ?,
                    address_line = ?, city = ?, state = ?, postcode = ?
                WHERE id = ?
            ');
            $stm->execute([
                $name, $phone, $profile_photo,
                $address_line, $city, $state, $postcode,
                $_user->id,
            ]);

            $_user->address_line = $address_line;
            $_user->city         = $city;
            $_user->state        = $state;
            $_user->postcode     = $postcode;
        }
        else {
            $stm = $_db->prepare('
                UPDATE user
                SET name = ?, phone = ?, profile_photo = ?
                WHERE id = ?
            ');
            $stm->execute([$name, $phone, $profile_photo, $_user->id]);
        }

        // (3) Update global user object (session)
        $_user->name          = $name;
        $_user->phone         = $phone;
        $_user->profile_photo = $profile_photo;

        temp('info', 'Profile updated successfully');
        redirect('/profile/profile.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Update Profile';
include '../_head.php';
?>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="email">Email</label>
    <?= html_text('email', 'readonly disabled') ?>
    <span></span>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="phone">Phone No.</label>
    <?= html_text('phone', 'maxlength="100"') ?>
    <?= err('phone') ?>

    <?php if ($is_member): ?>
        <label for="address_line">Address</label>
        <?= html_text('address_line', 'maxlength="255"') ?>
        <?= err('address_line') ?>

        <label for="city">City</label>
        <?= html_text('city', 'maxlength="100"') ?>
        <?= err('city') ?>

        <label for="state">State</label>
        <?= html_text('state', 'maxlength="100"') ?>
        <?= err('state') ?>

        <label for="postcode">Postcode</label>
        <?= html_text('postcode', 'maxlength="10"') ?>
        <?= err('postcode') ?>
    <?php endif ?>

    <label for="profile_photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('profile_photo', 'image/*', 'hidden') ?>
        <img src="/photos/<?= encode($profile_photo) ?>">
    </label>
    <?= err('profile_photo') ?>

    <section>
        <button type="button" data-get="/profile/profile.php">Cancel</button>
        <button>Save Changes</button>
        <button type="button" data-get="/profile/reset_password.php">Reset Password</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';