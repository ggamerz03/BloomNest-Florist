<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

// (1) Search & Filter
$keyword = req('keyword', '');
$role    = req('role', '');

// (2) Sorting
$fields = [
    'id'    => 'Id',
    'email' => 'Email',
    'name'  => 'Name',
    'phone' => 'Phone',
    'role'  => 'Role',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// (3) Paging
$page = req('page', 1);

require_once '../../lib/SimplePager.php';

$query = "SELECT * FROM user
          WHERE (email LIKE ? OR name LIKE ? OR phone LIKE ?)
          AND (role = ? OR ?)
          ORDER BY $sort $dir";

$params = ["%$keyword%", "%$keyword%", "%$keyword%", $role, $role == ''];

$p = new SimplePager($query, $params, 10, $page);
$arr = $p->result;

// Count total admins, to know when we're looking at the last one
$stm = $_db->prepare("SELECT COUNT(*) FROM user WHERE role = 'Admin'");
$stm->execute();
$admin_count = $stm->fetchColumn();

// Query string used to keep the current search/filter state on sort & pager links
$qs = 'keyword=' . urlencode($keyword) . "&role=$role";

// ----------------------------------------------------------------------------

$_title = 'User | Listing';
include '../../_head.php';
?>

<style>
    .popup {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
</style>

<p>
    <button data-get="insert.php">Insert User</button>
</p>

<form class="form">
    <label for="keyword">Keyword</label>
    <?= html_search('keyword', 'placeholder="Email / Name / Phone"') ?>
    <span></span>

    <label for="role">Role</label>
    <?= html_select('role', ['Admin' => 'Admin', 'Member' => 'Member'], 'All') ?>
    <span></span>

    <input type="hidden" name="sort" value="<?= $sort ?>">
    <input type="hidden" name="dir" value="<?= $dir ?>">

    <section>
        <button>Search</button>
    </section>
</form>

<p>
    <?= $p->count ?> of <?= $p->item_count ?> record(s) |
    Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<table class="table">
    <tr>
        <?= table_headers($fields, $sort, $dir, "$qs&page=$page") ?>
        <th></th>
    </tr>

    <?php foreach ($arr as $u): ?>
    <?php $is_last_admin = ($u->role == 'Admin' && $admin_count <= 1); ?>
    <tr data-get="detail.php?id=<?= $u->id ?>">
        <td><?= $u->id ?></td>
        <td><?= $u->email ?></td>
        <td><?= $u->name ?></td>
        <td><?= $u->phone ?></td>
        <td><?= $u->role ?></td>
        <td>
            <button data-get="update.php?id=<?= $u->id ?>">Update</button>
            <?php if (!$is_last_admin): ?>
            <button data-post="delete.php?id=<?= $u->id ?>" data-confirm="Delete this user?">Delete</button>
            <?php else: ?>
            <button disabled title="Cannot delete the last remaining admin">Delete</button>
            <?php endif ?>
            <img src="/photos/<?= $u->profile_photo ?>" class="popup">
        </td>
    </tr>
    <?php endforeach ?>
</table>

<br>

<?= $p->html("sort=$sort&dir=$dir&$qs") ?>

<?php
include '../../_foot.php';