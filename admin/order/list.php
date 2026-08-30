<?php
require '../../_base.php';
auth('Admin');

// ----------------------------------------------------------------------------

$stm = $_db->query("SELECT o.*, u.name AS user_name FROM orders o
                     JOIN user u ON o.user_id = u.id
                     ORDER BY o.order_date DESC");
$orders = $stm->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Order | List';
include '../../_head.php';
?>

<table class="table">
    <tr><th>Id</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr>
    <?php foreach ($orders as $o): ?>
    <tr>
        <td><?= $o->id ?></td>
        <td><?= encode($o->user_name) ?></td>
        <td><?= date('d M Y, h:i A', strtotime($o->order_date)) ?></td>
        <td>RM <?= number_format($o->total, 2) ?></td>
        <td><?= encode($o->status) ?></td>
        <td>
            <button data-get="detail.php?id=<?= $o->id ?>">View</button>
            <?php if ($o->status != 'Completed'): ?>
            <button data-post="update_status.php?id=<?= $o->id ?>" data-confirm="Mark this order as completed?">Complete</button>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach ?>

    <?php if (!$orders): ?>
    <tr><td colspan="6">No orders yet.</td></tr>
    <?php endif ?>
</table>

<?php
include '../../_foot.php';