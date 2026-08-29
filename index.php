<?php
require '_base.php';

// ----------------------------------------------------------------------------

if ($_user?->role == 'Admin') {
    $_title = 'Welcome';
    include '_head.php';
    ?>
    <p>Welcome back, <?= encode($_user->name) ?>. Manage your store below.</p>
    <section>
        <button data-get="/admin/product/list.php">Manage Products</button>
        <button data-get="/admin/member/list.php">Manage Members</button>
        <button data-get="/admin/order/list.php">Manage Orders</button>
    </section>
    <?php
    include '_foot.php';
    exit;
}

redirect('/cart/product_list.php');