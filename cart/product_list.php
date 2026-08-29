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

// (1) Search & Filter
$keyword     = req('keyword', '');
$category_id = req('category_id', '');

// (2) Sorting
$fields = [
    'name'       => 'Name',
    'unit_price' => 'Price',
    'stock_qty'  => 'Stock',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'name';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// (3) Paging
$page = req('page', 1);

require_once '../lib/SimplePager.php';

$query = "SELECT * FROM product
          WHERE (name LIKE ? OR description LIKE ?)
          AND (category_id = ? OR ?)
          AND status = 'In Stock'
          ORDER BY $sort $dir";

$params = ["%$keyword%", "%$keyword%", $category_id, $category_id == ''];

$p = new SimplePager($query, $params, 12, $page);
$arr = $p->result;

$categories = $_db->query('SELECT id, name FROM category ORDER BY name')
                   ->fetchAll(PDO::FETCH_KEY_PAIR);

$qs = 'keyword=' . urlencode($keyword) . "&category_id=$category_id";

// ----------------------------------------------------------------------------
$_title = 'Product | List';
include '../_head.php';
?>

<style>
    #products {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .product {
        border: 1px solid #ccc;
        border-radius: 6px;
        width: 220px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .product img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        cursor: pointer;
    }

    .product .info {
        padding: 8px 10px;
        flex-grow: 1;
    }

    .product .info .name {
        font-weight: bold;
        margin-bottom: 4px;
    }

    .product .info .price {
        color: #a33;
    }

    .product .info .stock {
        font-size: 0.9em;
        color: #555;
        margin-top: 4px;
    }

    .product .add-cart {
        padding: 8px 10px;
        border-top: 1px solid #ccc;
    }

    .qty-stepper {
        display: flex;
        align-items: center;
        justify-content: center;
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

    .sort-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }

    .sort-bar span.label {
        font-weight: bold;
        color: #555;
    }

    .sort-bar a {
        padding: 5px 12px;
        border: 1px solid #ccc;
        border-radius: 20px;
        text-decoration: none;
        color: #333;
        font-size: 0.9em;
    }

    .sort-bar a:hover {
        background: #f0f0f0;
    }

    .sort-bar a.active {
        background: #333;
        color: #fff;
        border-color: #333;
    }
</style>

<form class="form">
    <label for="keyword">Search</label>
    <?= html_search('keyword', 'placeholder="Product name / description"') ?>
    <span></span>

    <label for="category_id">Category</label>
    <?= html_select('category_id', $categories, 'All') ?>
    <span></span>

    <input type="hidden" name="sort" value="<?= $sort ?>">
    <input type="hidden" name="dir" value="<?= $dir ?>">

    <section>
        <button>Search</button>
    </section>
</form>

<div class="sort-bar">
    <span class="label">Sort by:</span>
    <?php foreach ($fields as $k => $v): ?>
        <?php
        $active = $sort == $k;
        $d = ($active && $dir == 'asc') ? 'desc' : 'asc';
        $arrow = $active ? ($dir == 'asc' ? ' ↑' : ' ↓') : '';
        ?>
        <a href="?sort=<?= $k ?>&dir=<?= $d ?>&<?= $qs ?>&page=<?= $page ?>"
           class="<?= $active ? 'active' : '' ?>">
            <?= $v . $arrow ?>
        </a>
    <?php endforeach ?>
</div>

<p>
    <?= $p->count ?> of <?= $p->item_count ?> product(s) |
    Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<div id="products">
    <?php foreach ($arr as $prod): ?>
        <?php
        $cart = get_cart();
        $id   = $prod->id;
        $unit = $cart[$prod->id] ?? 0;
        $max  = min(10, $prod->stock_qty);
        ?>
        <div class="product">
            <img src="/prod_photos/<?= $prod->photo ?>"
                 data-get="product_detail.php?id=<?= $prod->id ?>">

            <div class="info">
                <div class="name"><?= encode($prod->name) ?></div>
                <div class="price">RM <?= number_format($prod->unit_price, 2) ?></div>
                <div class="stock">
                    <?= $prod->stock_qty > 0 ? "{$prod->stock_qty} in stock" : 'Out of stock' ?>
                </div>
            </div>

            <div class="add-cart">
                <?php if ($prod->stock_qty > 0): ?>
                    <?php if ($_user): ?>
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
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $prod->id ?>">
                            <input type="hidden" name="unit" value="1">
                            <button>Add to Cart</button>
                        </form>
                    <?php endif ?>
                <?php else: ?>
                    <button disabled>Out of Stock</button>
                <?php endif ?>
            </div>
        </div>
    <?php endforeach ?>

    <?php if (!count($arr)): ?>
        <p>No products found.</p>
    <?php endif ?>
</div>

<br>

<?= $p->html("sort=$sort&dir=$dir&$qs") ?>

<?php
include '../_foot.php';