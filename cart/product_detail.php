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
            ?>
            <?php if ($_user): ?>
                <form method="post">
                    <?= html_hidden('id') ?>
                    <?= html_select('unit', $_units, '') ?>
                    <?= $unit ? '✅' : '' ?>
                </form>
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

<script>
    $('select').on('change', e => e.target.form.submit());
</script>

<?php
include '../_foot.php';