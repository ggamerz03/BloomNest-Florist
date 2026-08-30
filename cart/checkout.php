<?php
require '../_base.php';
auth('Member');

// ----------------------------------------------------------------------------

$cart = get_cart();
if (!$cart) redirect('cart.php');

if (!has_address($_user)) {
    temp('info', 'Please add a delivery address before checking out');
    redirect('/profile/address.php');
}

if (is_post()) {
    redirect('payment.php');
}

$ids = array_keys($cart);
$in  = implode(',', array_fill(0, count($ids), '?'));
$stm = $_db->prepare("SELECT * FROM product WHERE id IN ($in)");
$stm->execute($ids);

$items    = [];
$subtotal = 0;

foreach ($stm->fetchAll() as $p) {
    $unit      = $cart[$p->id];
    $line      = $p->unit_price * $unit;
    $subtotal += $line;
    $items[]   = (object)['product' => $p, 'unit' => $unit, 'subtotal' => $line];
}

$delivery_fee = 5.00;
$total = $subtotal + $delivery_fee;

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

<p>
    Subtotal: RM <?= number_format($subtotal, 2) ?><br>
    Delivery Fee: RM <?= number_format($delivery_fee, 2) ?><br>
    <strong>Total: RM <?= number_format($total, 2) ?></strong>
</p>

<h3>Delivery Address</h3>
<p>
    <?= encode($_user->name) ?>, <?= encode($_user->phone) ?><br>
    <?= encode(format_address($_user)) ?>
</p>
<p>
    <button type="button" data-get="cart.php">Cancel</button>
    <button data-get="/profile/address.php">Change Address</button>
</p>

<form method="post">
    <p>
        <button>Proceed to Payment</button>
    </p>
</form>

<?php
include '../_foot.php';