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

$ids = array_keys($cart);
$in  = implode(',', array_fill(0, count($ids), '?'));
$stm = $_db->prepare("SELECT * FROM product WHERE id IN ($in)");
$stm->execute($ids);
$products = $stm->fetchAll();

$line_items = [];
foreach ($products as $p) {
    $line_items[] = [
        'price_data' => [
            'currency'     => 'myr',
            'product_data' => ['name' => $p->name],
            'unit_amount'  => (int)round($p->unit_price * 100),
        ],
        'quantity' => $cart[$p->id],
    ];
}

$line_items[] = [
    'price_data' => [
        'currency'     => 'myr',
        'product_data' => ['name' => 'Delivery Fee'],
        'unit_amount'  => 500,
    ],
    'quantity' => 1,
];

$session = stripe_request('POST', 'checkout/sessions', [
    'payment_method_types' => ['card'],
    'line_items'           => $line_items,
    'mode'                 => 'payment',
    'customer_email'       => $_user->email,
    'success_url'          => base('cart/stripe_success.php') . '?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'           => base('cart/cart.php'),
]);

if (isset($session->url)) {
    redirect($session->url);
}

temp('info', 'Unable to start payment. Please try again.');
redirect('checkout.php');