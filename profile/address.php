<?php
require '../_base.php';
auth('Member');

// ----------------------------------------------------------------------------

if (is_post()) {
    $address_line = req('address_line');
    $city         = req('city');
    $state        = req('state');
    $postcode     = req('postcode');

    if (!$address_line) $_err['address_line'] = 'Required';
    if (!$city) $_err['city'] = 'Required';
    if (!$state) $_err['state'] = 'Required';
    if (!$postcode) $_err['postcode'] = 'Required';

    if (!$_err) {
        $stm = $_db->prepare('UPDATE user SET address_line=?, city=?, state=?, postcode=? WHERE id = ?');
        $stm->execute([$address_line, $city, $state, $postcode, $_user->id]);
        $_SESSION['user']->address_line = $address_line;
        $_SESSION['user']->city         = $city;
        $_SESSION['user']->state        = $state;
        $_SESSION['user']->postcode     = $postcode;
        temp('info', 'Address saved');
        redirect('/cart/checkout.php');
    }
}

$address_line = $_user->address_line;
$city         = $_user->city;
$state        = $_user->state;
$postcode     = $_user->postcode;

// ----------------------------------------------------------------------------
$_title = 'Delivery Address';
include '../_head.php';
?>

<form method="post" class="form">
    <label for="address_line">Address</label>
    <?= html_text('address_line') ?>
    <?= err('address_line') ?>

    <label for="city">City</label>
    <?= html_text('city') ?>
    <?= err('city') ?>

    <label for="state">State</label>
    <?= html_text('state') ?>
    <?= err('state') ?>

    <label for="postcode">Postcode</label>
    <?= html_text('postcode') ?>
    <?= err('postcode') ?>

    <section>
        <button type="button" data-get="/cart/checkout.php">Cancel</button>
        <button>Save</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';