<?php
require '../_base.php';
auth('Member');

// ----------------------------------------------------------------------------

$id = req('id');

$stm = $_db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$stm->execute([$id, $_user->id]);
$o = $stm->fetch();
if (!$o) redirect('history.php');

$stm = $_db->prepare('SELECT oi.*, p.name, p.photo
                       FROM order_item oi JOIN product p ON oi.product_id = p.id
                       WHERE oi.order_id = ?');
$stm->execute([$id]);
$items = $stm->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Order | Detail';
include '../_head.php';
?>

<table class="table detail">
    <tr><th>Order Id</th><td><?= $o->id ?></td></tr>
    <tr><th>Date</th><td><?= date('d M Y, h:i A', strtotime($o->order_date)) ?></td></tr>
    <tr><th>Status</th><td><?= encode($o->status) ?></td></tr>
    <tr><th>Deliver To</th><td><?= encode($o->delivery_address) ?></td></tr>
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
    <button data-get="history.php">Back to History</button>
</p>

<?php
include '../_foot.php';