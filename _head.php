<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?> | BloomNest Florist</title>
    <link rel="shortcut icon" href="/images/logo.png">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <?php if ($_user?->role == 'Member' && !has_address($_user)): ?>
    <div id="address-banner" style="background:#fff3cd;border:1px solid #ffeeba;padding:10px 15px;position:relative;">
        You haven't added a delivery address yet.
        <a href="/profile/address.php">Add one now</a>.
        <span id="address-banner-close" style="position:absolute;right:15px;top:8px;cursor:pointer;font-weight:bold;">&times;</span>
    </div>
    <?php endif ?>

    <header>
        <h1>
            <a href="/">
                &nbsp;BloomNest Florist
            </a>
        </h1>

        <?php if ($_user): ?>
            <div>
                <?= encode($_user->name) ?><br>
                <?= encode($_user->role) ?>
            </div>
            <img src="/photos/<?= encode($_user->profile_photo) ?>" alt="Photo" style="width:50px;height:50px;border-radius:50%;">
        <?php endif ?>
    </header>

    <nav>
        <!-- Left side -->
        <?php if (!$_user): ?>
            <a href="/index.php">Home</a>
        <?php endif ?>

        <?php if ($_user?->role == 'Admin'): ?>
            <a href="/index.php">Home</a>
            <a href="/admin/product/list.php">Product</a>
            <a href="/admin/member/list.php">Member</a>
            <a href="/admin/order/list.php">
                Order
                <?php
                    $stm = $_db->query("SELECT COUNT(*) FROM orders WHERE status = 'To Ship'");
                    $pending = $stm->fetchColumn();
                    if ($pending) echo "($pending)";
                ?>
            </a>
        <?php endif ?>

        <?php if ($_user?->role == 'Member'): ?>
            <a href="/cart/product_list.php">Home</a>
            <a href="/cart/cart.php">
                Shopping Cart
                <?php
                    $cart = get_cart();
                    $count = array_sum($cart);
                    if ($count) echo "($count)";
                ?>
            </a>
            <a href="/order/history.php">Order History</a>
        <?php endif ?>

        <div></div>

        <!-- Right side -->
        <?php if ($_user): ?>
            <a href="/profile/profile.php">Profile</a>
            <a href="/auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="/auth/register.php">Register</a>
            <a href="/auth/login.php">Login</a>
        <?php endif ?>
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>