<?php
require '../_base.php';
auth('Member');

// ----------------------------------------------------------------------------

$stm = $_db->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC');
$stm->execute([$_user->id]);
$orders = $stm->fetchAll();

foreach ($orders as $o) {
    $stm2 = $_db->prepare('SELECT oi.*, p.name, p.photo
                            FROM order_item oi JOIN product p ON oi.product_id = p.id
                            WHERE oi.order_id = ?');
    $stm2->execute([$o->id]);
    $o->items = $stm2->fetchAll();
}

// ----------------------------------------------------------------------------
$_title = 'Order History';
include '../_head.php';
?>

<style>
    .order-card { border: 1px solid #ccc; border-radius: 6px; padding: 12px; margin-bottom: 15px; }
    .order-card .order-header { font-weight: bold; margin-bottom: 8px; }
    .order-card .order-address { font-size: 0.9em; color: #555; margin-bottom: 8px; }
</style>

<?php foreach ($orders as $o): ?>
<div class="order-card">
    <div class="order-header">
        Order #<?= $o->id ?> &middot; <?= date('d M Y, h:i A', strtotime($o->order_date)) ?> &middot; Status: <?= encode($o->status) ?>
    </div>

    <div class="order-address">
        Deliver to: <?= encode($o->delivery_address) ?>
    </div>

    <table class="table">
        <tr><th>Photo</th><th>Name</th><th>Qty</th><th>Subtotal</th></tr>
        <?php foreach ($o->items as $item): ?>
        <tr>
            <td><img src="/prod_photos/<?= $item->photo ?>" style="width:50px;height:50px;object-fit:cover;"></td>
            <td><?= encode($item->name) ?></td>
            <td><?= $item->qty ?></td>
            <td>RM <?= number_format($item->subtotal, 2) ?></td>
        </tr>
        <?php endforeach ?>
    </table>

    <p>
        Delivery Fee: RM <?= number_format($o->delivery_fee, 2) ?> |
        <strong>Total: RM <?= number_format($o->total, 2) ?></strong>
    </p>
</div>
<?php endforeach ?>

<?php if (!$orders): ?>
<p>You have no orders yet.</p>
<?php endif ?>

<?php
include '../_foot.php';