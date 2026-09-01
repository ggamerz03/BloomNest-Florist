<?php
require '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

$id = req('id');

$stm = $_db->prepare('SELECT p.*, c.name AS category_name
                       FROM product p JOIN category c ON p.category_id = c.id
                       WHERE p.id = ?');
$stm->execute([$id]);
$p = $stm->fetch();

if (!$p) {
    redirect('list.php');
}

// ----------------------------------------------------------------------------

$_title = 'Product | Detail';
include '../../_head.php';
?>

<table class="table detail">
    <tr>
        <th>Id</th>
        <td><?= $p->id ?></td>
    </tr>
    <tr>
        <th>Category</th>
        <td><?= encode($p->category_name) ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= encode($p->name) ?></td>
    </tr>
    <tr>
        <th>Color</th>
        <td><?= encode($p->color) ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?= encode($p->description) ?></td>
    </tr>
    <tr>
        <th>Unit Price</th>
        <td>RM <?= number_format($p->unit_price, 2) ?></td>
    </tr>
    <tr>
        <th>Stock Qty</th>
        <td><?= $p->stock_qty ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td><?= $p->stock_qty > 0 ? 'In Stock' : 'Out of Stock' ?></td>
    </tr>
    <tr>
        <th>Photo</th>
        <td>
            <img src="/prod_photos/<?= $p->photo ?>" alt="Photo"
                 style="width:150px;height:150px;object-fit:cover;border:1px solid #333;">
        </td>
    </tr>
</table>

<p>
    <button data-get="list.php">Back to List</button>
    <button data-get="update.php?id=<?= $p->id ?>">Update</button>
</p>

<?php
include '../../_foot.php';