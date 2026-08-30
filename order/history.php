<?php
require '../_base.php';
auth('Member');

// ----------------------------------------------------------------------------

$stm = $_db->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC');
$stm->execute([$_user->id]);
$orders = $stm->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Order History';
include '../_head.php';
?>

<style>
    .order-card { border: 1px solid #ccc; border-radius: 6px; padding: 12px; margin-bottom: 15px; cursor: pointer; }
    .order-card .order-header { font-weight: bold; margin-bottom: 4px; }
    .order-card .order-address { font-size: 0.9em; color: #555; }
</style>

<?php foreach ($orders as $o): ?>
<div class="order-card" data-get="detail.php?id=<?= $o->id ?>">
    <div class="order-header">
        Order #<?= $o->id ?> &middot; <?= date('d M Y, h:i A', strtotime($o->order_date)) ?> &middot; Status: <?= encode($o->status) ?>
    </div>
    <div class="order-address">
        Deliver to: <?= encode($o->delivery_address) ?>
    </div>
    <p><strong>Total: RM <?= number_format($o->total, 2) ?></strong></p>
</div>
<?php endforeach ?>

<?php if (!$orders): ?>
<p>You have no orders yet.</p>
<?php endif ?>

<?php
include '../_foot.php';