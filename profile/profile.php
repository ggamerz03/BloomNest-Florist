<?php
include '../_base.php';

// ----------------------------------------------------------------------------

// Authenticated users
auth();

$stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
$stm->execute([$_user->id]);
$u = $stm->fetch();

if (!$u) {
    redirect('/');
}

// ----------------------------------------------------------------------------

$_title = 'My Profile';
include '../_head.php';
?>

<table class="table detail">
    <tr>
        <th>Email</th>
        <td><?= encode($u->email) ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= encode($u->name) ?></td>
    </tr>
    <tr>
        <th>Phone No.</th>
        <td><?= encode($u->phone) ?></td>
    </tr>
    <tr>
        <th>Photo</th>
        <td>
            <img src="/photos/<?= encode($u->profile_photo) ?>" alt="Profile Photo"
                 style="width:180px;height:180px;object-fit:cover;border:1px solid #333;">
        </td>
    </tr>
</table>

<section>
    <button data-get="/profile/update_profile.php">Update Profile</button>
    <button data-get="/">Back to Home</button>
</section>

<?php
include '../_foot.php';