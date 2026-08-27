<?php
require '_base.php';

$_title = 'Main Page';
include '_head.php';
?>

    <h1>Welcome to BloomNest Florist</h1>
    <p>Discover our beautiful selection of flowers and plants!</p>
    <button><a href="/page/productList.php">View Products</a></button>
    <button><a href="/page/shoppingCart.php">View Shopping Cart</a></button>
    <button><a href="/page/login.php">Login</a></button>


<?php 
include '_foot.php';