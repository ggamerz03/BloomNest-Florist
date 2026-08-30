<?php
require '../../_base.php';
auth('Admin');

// ----------------------------------------------------------------------------

$id = req('id');

$stm = $_db->prepare('SELECT o.*, u.name AS user_name, u.email, u.phone
                       FROM orders o JOIN user u ON o.user_id = u.id
                       WHERE o.id = ?');
$stm->execute([$id]);
$o = $stm->fetch();
if (!$o) redirect('list.php');

$stm = $_db->prepare('SELECT oi.*, p.name, p.photo
                       FROM order_item oi JOIN product p ON oi.product_id = p.id
                       WHERE oi.order_id = ?');
$stm->execute([$id]);
$items = $stm->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Order | Detail';
include '../../_head.php';
?>

<table class="table detail">
    <tr><th>Order Id</th><td><?= $o->id ?></td></tr>
    <tr><th>Customer</th><td><?= encode($o->user_name) ?> (<?= encode($o->email) ?>)</td></tr>
    <tr><th>Date</th><td><?= date('d M Y, h:i A', strtotime($o->order_date)) ?></td></tr>
    <tr><th>Status</th><td><?= encode($o->status) ?></td></tr>
    <tr>
        <th>Deliver To</th>
        <td>
            <?= encode($o->user_name) ?>, <?= encode($o->phone) ?><br>
            <?= encode($o->delivery_address) ?>
        </td>
    </tr>
</table>

<table class="table">
    <tr><th>Photo</th><th>Name</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th></tr>
    <?php foreach ($items as $item): ?>
    <tr>
        <td><img src="/prod_photos/<?= $item->photo ?>" style="width:50px;height:50px;object-fit:cover;"></td>
        <td><?= encode($item->name) ?></td>
        <td>RM <?= number_format($item->unit_price, 2) ?></td>
        <td><?= $item->qty ?></td>
        <td>RM <?= number_format($item->subtotal, 2) ?></td>
    </tr>
    <?php endforeach ?>
</table>

<p>
    Subtotal: RM <?= number_format($o->subtotal, 2) ?><br>
    Delivery Fee: RM <?= number_format($o->delivery_fee, 2) ?><br>
    <strong>Total: RM <?= number_format($o->total, 2) ?></strong>
</p>

<p>
    <button data-get="list.php">Back to List</button>
    <?php if ($o->status != 'Completed'): ?>
    <button data-post="update_status.php?id=<?= $o->id ?>" data-confirm="Mark this order as completed?">Complete</button>
    <?php endif ?>
</p>

<?php
include '../../_foot.php';