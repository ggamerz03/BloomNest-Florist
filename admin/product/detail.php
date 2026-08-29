<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

$id = req('id');

$stm = $_db->prepare('
    SELECT product.*, category.name AS category_name
    FROM product
    JOIN category ON category.id = product.category_id
    WHERE product.id = ?
');
$stm->execute([$id]);
$prod = $stm->fetch();

if (!$prod) {
    redirect('list.php');
}

// ----------------------------------------------------------------------------

$_title = 'Product | Detail';
include '../../_head.php';
?>

<table class="table detail">
    <tr>
        <th>Category</th>
        <td><?= $prod->category_name ?></td>
    </tr>
    <tr>
        <th>Id</th>
        <td><?= $prod->id ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= $prod->name ?></td>
    </tr>
    <tr>
        <th>Color</th>
        <td><?= $prod->color ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?= $prod->description ?></td>
    </tr>
    <tr>
        <th>Unit Price</th>
        <td>RM <?= number_format($prod->unit_price, 2) ?></td>
    </tr>
    <tr>
        <th>Stock Qty</th>
        <td><?= $prod->stock_qty ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td><?= $prod->status ?></td>
    </tr>
    <tr>
        <th>Photo</th>
        <td>
            <img src="/photos/<?= $prod->photo ?>" alt="Photo"
                 style="width:150px;height:150px;object-fit:cover;border:1px solid #333;">
        </td>
    </tr>
</table>

<p>
    <button data-get="update.php?id=<?= $prod->id ?>">Update</button>
    <button data-get="list.php">Back to List</button>
</p>

<?php
include '../../_foot.php';