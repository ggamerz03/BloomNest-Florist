<?php
require '../_base.php';
auth('Member');

// ----------------------------------------------------------------------------

if (is_post()) {
    $id   = req('id');
    $unit = req('unit');
    update_cart($id, $unit);
    redirect();
}

$cart  = get_cart();
$items = [];
$total = 0;

if ($cart) {
    $ids = array_keys($cart);
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $stm = $_db->prepare("SELECT * FROM product WHERE id IN ($in)");
    $stm->execute($ids);

    foreach ($stm->fetchAll() as $p) {
        $unit     = $cart[$p->id];
        $subtotal = $p->unit_price * $unit;
        $total   += $subtotal;
        $items[]  = (object)['product' => $p, 'unit' => $unit, 'subtotal' => $subtotal];
    }
}

// ----------------------------------------------------------------------------
$_title = 'Shopping Cart';
include '../_head.php';
?>

<style>
    .qty-stepper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .qty-stepper form {
        display: inline;
    }

    .qty-stepper button {
        width: 26px;
        height: 26px;
        line-height: 1;
        font-size: 1em;
        border-radius: 4px;
    }

    .qty-stepper .qty-value {
        min-width: 18px;
        text-align: center;
        font-weight: bold;
    }
</style>

<table class="table">
    <tr>
        <th>Photo</th><th>Name</th><th>Price</th><th>Unit</th><th>Subtotal</th><th></th>
    </tr>

    <?php foreach ($items as $item): ?>
    <?php
    $p    = $item->product;
    $unit = $item->unit;
    $id   = $p->id;
    $max  = min(10, $p->stock_qty);
    ?>
    <tr>
        <td><img src="/prod_photos/<?= $p->photo ?>" style="width:60px;height:60px;object-fit:cover;"></td>
        <td><?= encode($p->name) ?></td>
        <td>RM <?= number_format($p->unit_price, 2) ?></td>
        <td>
            <div class="qty-stepper">
                <form method="post">
                    <?= html_hidden('id') ?>
                    <input type="hidden" name="unit" value="<?= max(0, $unit - 1) ?>">
                    <button type="submit">−</button>
                </form>

                <span class="qty-value"><?= $unit ?></span>

                <form method="post">
                    <?= html_hidden('id') ?>
                    <input type="hidden" name="unit" value="<?= min($max, $unit + 1) ?>">
                    <button type="submit" <?= $unit >= $max ? 'disabled' : '' ?>>+</button>
                </form>
            </div>
        </td>
        <td>RM <?= number_format($item->subtotal, 2) ?></td>
        <td>
            <form method="post">
                <input type="hidden" name="id" value="<?= $p->id ?>">
                <input type="hidden" name="unit" value="0">
                <button data-confirm="Remove this item?">Remove</button>
            </form>
        </td>
    </tr>
    <?php endforeach ?>

    <?php if (!$items): ?>
    <tr><td colspan="6">Your cart is empty.</td></tr>
    <?php endif ?>
</table>

<p><strong>Total: RM <?= number_format($total, 2) ?></strong></p>

<p>
    <button data-get="product_list.php">Continue Shopping</button>
    <?php if ($items): ?>
    <button data-get="checkout.php">Checkout</button>
    <?php endif ?>
</p>

<?php
include '../_foot.php';