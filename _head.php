<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?> | BloomNest Florist</title>
    <link rel="shortcut icon" href="/images/BloomNest LOGO.png">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <header>
        <h1>
            <a href="/">
                <img src="/images/BloomNest LOGO.png" alt="BloomNest Florist Logo" width="200" height="100">
                BloomNest Florist
            </a>
        </h1>

        <?php if ($_user): ?>
            <div>
                <?= encode($_user->name) ?><br>
                <?= encode($_user->role) ?>
            </div>
            <img src="/uploads/profiles/<?= encode($_user->profile_photo) ?>" alt="Photo" style="width:50px;height:50px;border-radius:50%;">
        <?php endif ?>
    </header>

    <nav>
        <!-- Left side -->
        <?php if (!$_user || $_user->role == 'Admin' || $_user->role == 'Member'): ?>
            <a href="/index.php">Home</a>
        <?php endif ?>

        <?php if ($_user?->role == 'Admin'): ?>
            <a href="/admin/product/list.php">Product</a>
            <a href="/admin/member/list.php">Member</a>
            <a href="/admin/order/list.php">Order</a>
        <?php endif ?>

        <?php if ($_user?->role == 'Member'): ?>
            <a href="/cart/product_list.php">Shopping Cart</a>
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