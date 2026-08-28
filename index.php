<?php
require '_base.php';

$_title = 'Welcome';
include '_head.php';
?>

<?php if (!$_user): ?>

    <p>Discover our beautiful selection of fresh flowers and plants, arranged for every occasion.</p>
    <section>
        <button data-get="/auth/register.php">Register</button>
        <button data-get="/auth/login.php">Login</button>
    </section>

<?php elseif ($_user->role == 'Admin'): ?>

    <p>Welcome back, <?= encode($_user->name) ?>. Manage your store below.</p>
    <section>
        <button data-get="/admin/product/list.php">Manage Products</button>
        <button data-get="/admin/member/list.php">Manage Members</button>
        <button data-get="/admin/order/list.php">Manage Orders</button>
    </section>

<?php else: ?>

    <p>Welcome back, <?= encode($_user->name) ?>. Start shopping for fresh flowers today!</p>
    <section>
        <button data-get="/cart/product_list.php">Shop Now</button>
        <button data-get="/order/history.php">My Orders</button>
    </section>

<?php endif ?>

<?php
include '_foot.php';