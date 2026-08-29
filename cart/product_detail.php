<?php
require '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    if (!$_user) {
        temp('info', 'Please login to add items to your cart');
        redirect('/auth/login.php');
    }

    $id   = req('id');
    $unit = req('unit');
    update_cart($id, $unit);
    redirect();
}

$id  = req('id');
$stm = $_db->prepare('SELECT * FROM product WHERE id = ?');
$stm->execute([$id]);
$p = $stm->fetch();
if (!$p) redirect('product_list.php');

// ----------------------------------------------------------------------------
$_title = 'Product | Detail';
include '../_head.php';
?>

<style>
    #photo { display: block; border: 1px solid #333; width: 200px; height: 200px; object-fit: cover; }

    .qty-stepper {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .qty-stepper form {
        display: inline;
    }

    .qty-stepper button {
        width: 30px;
        height: 30px;
        line-height: 1;
        font-size: 1.1em;
        border-radius: 4px;
    }

    .qty-stepper .qty-value {
        min-width: 20px;
        text-align: center;
        font-weight: bold;
    }
</style>

<p>
    <img src="/prod_photos/<?= $p->photo ?>" id="photo">
</p>

<table class="table detail">
    <tr><th>Id</th><td><?= $p->id ?></td></tr>
    <tr><th>Name</th><td><?= encode($p->name) ?></td></tr>
    <tr><th>Description</th><td><?= encode($p->description) ?></td></tr>
    <tr><th>Price</th><td>RM <?= number_format($p->unit_price, 2) ?></td></tr>
    <tr><th>Stock</th><td><?= $p->stock_qty ?> available</td></tr>
    <tr>
        <th>Unit</th>
        <td>
            <?php
            $cart = get_cart();
            $id   = $p->id;
            $unit = $cart[$p->id] ?? 0;
            $max  = min(10, $p->stock_qty);
            ?>
            <?php if ($_user): ?>
                <?php if ($p->stock_qty > 0): ?>
                    <div class="qty-stepper">
                        <form method="post">
                            <?= html_hidden('id') ?>
                            <input type="hidden" name="unit" value="<?= max(0, $unit - 1) ?>">
                            <button type="submit" <?= $unit <= 0 ? 'disabled' : '' ?>>−</button>
                        </form>

                        <span class="qty-value"><?= $unit ?></span>

                        <form method="post">
                            <?= html_hidden('id') ?>
                            <input type="hidden" name="unit" value="<?= min($max, $unit + 1) ?>">
                            <button type="submit" <?= $unit >= $max ? 'disabled' : '' ?>>+</button>
                        </form>
                    </div>
                <?php else: ?>
                    Out of stock
                <?php endif ?>
            <?php else: ?>
                <form method="post">
                    <input type="hidden" name="id" value="<?= $p->id ?>">
                    <input type="hidden" name="unit" value="1">
                    <button>Add to Cart</button>
                </form>
            <?php endif ?>
        </td>
    </tr>
</table>

<p>
    <button data-get="product_list.php">List</button>
    <?php if ($_user): ?>
    <button data-get="cart.php">View Cart</button>
    <?php endif ?>
</p>

<?php
include '../_foot.php';