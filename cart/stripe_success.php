<?php
require '../_base.php';
auth('Member');

// ----------------------------------------------------------------------------

$session_id = req('session_id');
if (!$session_id) redirect('cart.php');

$session = stripe_request('GET', "checkout/sessions/$session_id");

if (!isset($session->payment_status) || $session->payment_status !== 'paid') {
    temp('info', 'Payment was not completed');
    redirect('cart.php');
}

$cart = get_cart();

if (!$cart) {
    temp('info', 'Order has been placed');
    redirect('/order/history.php');
}

$ids = array_keys($cart);
$in  = implode(',', array_fill(0, count($ids), '?'));
$stm = $_db->prepare("SELECT * FROM product WHERE id IN ($in)");
$stm->execute($ids);
$products = $stm->fetchAll();

$subtotal = 0;
foreach ($products as $p) {
    $subtotal += $p->unit_price * $cart[$p->id];
}
$delivery_fee = 5.00;
$total = $subtotal + $delivery_fee;

$stm = $_db->prepare('INSERT INTO orders (user_id, order_date, delivery_address, subtotal, delivery_fee, total, status, stripe_session_id)
                       VALUES (?, NOW(), ?, ?, ?, ?, ?, ?)');
$stm->execute([$_user->id, format_address($_user), $subtotal, $delivery_fee, $total, 'To Ship', $session_id]);
$order_id = $_db->lastInsertId();

$stm_item  = $_db->prepare('INSERT INTO order_item (order_id, product_id, unit_price, qty, subtotal)
                             VALUES (?, ?, ?, ?, ?)');
$stm_stock = $_db->prepare('UPDATE product SET stock_qty = stock_qty - ? WHERE id = ?');

foreach ($products as $p) {
    $qty = $cart[$p->id];
    $stm_item->execute([$order_id, $p->id, $p->unit_price, $qty, $p->unit_price * $qty]);
    $stm_stock->execute([$qty, $p->id]);
}

set_cart([]);
temp('info', 'Order has been placed');
redirect('/order/history.php');