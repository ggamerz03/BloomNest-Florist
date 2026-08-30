<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

$id = req('id');

$stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
$stm->execute([$id]);
$u = $stm->fetch();

if (!$u) {
    redirect('list.php');
}

// ----------------------------------------------------------------------------

$_title = 'Member | Detail';
include '../../_head.php';
?>

<table class="table detail">
    <tr>
        <th>Id</th>
        <td><?= $u->id ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= $u->email ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $u->name ?></td>
    </tr>
    <tr>
        <th>Phone No.</th>
        <td><?= $u->phone ?></td>
    </tr>
    <tr>
        <th>Role</th>
        <td><?= $u->role ?></td>
    </tr>
    <tr>
        <th>Photo</th>
        <td>
            <img src="/photos/<?= $u->profile_photo ?>" alt="Photo"
                 style="width:150px;height:150px;object-fit:cover;border:1px solid #333;">
        </td>
    </tr>
</table>

<p>
    <button data-get="list.php">Back to List</button>
    <button data-get="update.php?id=<?= $u->id ?>">Update</button>
    <button data-post="delete.php?id=<?= $u->id ?>" data-confirm="Delete this user?">Delete</button>
</p>

<?php
include '../../_foot.php';