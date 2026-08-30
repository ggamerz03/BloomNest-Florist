<?php
require '../../_base.php';
auth('Admin');

// ----------------------------------------------------------------------------

$keyword   = req('keyword', '');
$date_from = req('date_from', '');
$date_to   = req('date_to', '');

$sort = req('sort');
in_array($sort, ['order_date', 'total', 'status']) || $sort = 'order_date';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'desc';

$query = "SELECT o.*, u.name AS user_name FROM orders o
          JOIN user u ON o.user_id = u.id
          WHERE (o.id LIKE ? OR u.name LIKE ? OR u.email LIKE ?)
          AND (DATE(o.order_date) >= ? OR ?)
          AND (DATE(o.order_date) <= ? OR ?)
          ORDER BY $sort $dir";

$params = ["%$keyword%", "%$keyword%", "%$keyword%", $date_from, $date_from == '', $date_to, $date_to == ''];

$stm = $_db->prepare($query);
$stm->execute($params);
$orders = $stm->fetchAll();

$qs = 'keyword=' . urlencode($keyword) . "&date_from=$date_from&date_to=$date_to";

// ----------------------------------------------------------------------------
$_title = 'Order | List';
include '../../_head.php';
?>

<form class="form">
    <label for="keyword">Keyword</label>
    <?= html_search('keyword', 'placeholder="Order Id / Customer Name / Email"') ?>
    <span></span>

    <label for="date_from">From</label>
    <input type="date" id="date_from" name="date_from" value="<?= $date_from ?>">
    <span></span>

    <label for="date_to">To</label>
    <input type="date" id="date_to" name="date_to" value="<?= $date_to ?>">
    <span></span>

    <input type="hidden" name="sort" value="<?= $sort ?>">
    <input type="hidden" name="dir" value="<?= $dir ?>">

    <section>
        <button>Search</button>
    </section>
</form>

<table class="table">
    <tr>
        <?= table_headers([
            'order_date' => 'Date',
            'total'      => 'Total',
            'status'     => 'Status',
        ], $sort, $dir, $qs) ?>
        <th>Id</th><th>Customer</th><th></th>
    </tr>
    <?php foreach ($orders as $o): ?>
    <tr>
        <td><?= date('d M Y, h:i A', strtotime($o->order_date)) ?></td>
        <td>RM <?= number_format($o->total, 2) ?></td>
        <td><?= encode($o->status) ?></td>
        <td><?= $o->id ?></td>
        <td><?= encode($o->user_name) ?></td>
        <td>
            <button data-get="detail.php?id=<?= $o->id ?>">View</button>
            <?php if ($o->status != 'Completed'): ?>
            <button data-post="update_status.php?id=<?= $o->id ?>" data-confirm="Mark this order as completed?">Complete</button>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach ?>

    <?php if (!$orders): ?>
    <tr><td colspan="6">No orders found.</td></tr>
    <?php endif ?>
</table>

<?php
include '../../_foot.php';