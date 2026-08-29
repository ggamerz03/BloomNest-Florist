<?php
require '../_base.php';
auth('Member');

// ----------------------------------------------------------------------------

$cart = get_cart();
if (!$cart) redirect('cart.php');

$ids = array_keys($cart);
$in  = implode(',', array_fill(0, count($ids), '?'));
$stm = $_db->prepare("SELECT * FROM product WHERE id IN ($in)");
$stm->execute($ids);

$items = [];
$total = 0;

foreach ($stm->fetchAll() as $p) {
    $unit     = $cart[$p->id];
    $subtotal = $p->unit_price * $unit;
    $total   += $subtotal;
    $items[]  = (object)['product' => $p, 'unit' => $unit, 'subtotal' => $subtotal];
}

// ----------------------------------------------------------------------------
$_title = 'Checkout';
include '../_head.php';
?>

<table class="table">
    <tr><th>Name</th><th>Qty</th><th>Subtotal</th></tr>
    <?php foreach ($items as $item): ?>
    <tr>
        <td><?= encode($item->product->name) ?></td>
        <td><?= $item->unit ?></td>
        <td>RM <?= number_format($item->subtotal, 2) ?></td>
    </tr>
    <?php endforeach ?>
</table>

<p><strong>Total: RM <?= number_format($total, 2) ?></strong></p>

<p>
    <button data-get="payment.php">Proceed to Payment</button>
</p>

<?php
include '../_foot.php';