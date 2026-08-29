<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

$categories = $_db->query('SELECT id, name FROM category ORDER BY name')->fetchAll(PDO::FETCH_KEY_PAIR);

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM product WHERE id = ?');
    $stm->execute([$id]);
    $prod = $stm->fetch();

    if (!$prod) {
        redirect('list.php');
    }

    extract((array)$prod);
    $_SESSION['photo'] = $prod->photo;
}

if (is_post()) {
    $id          = req('id');
    $name        = req('name');
    $color       = req('color');
    $description = req('description');
    $unit_price  = req('unit_price');
    $stock_qty   = req('stock_qty');
    $status      = req('status');
    $category_id = req('category_id');
    $photo = $_SESSION['photo'];
    $f = get_file('photo');

    // Validate: name
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    // Validate: color
    if ($color == '') {
        $_err['color'] = 'Required';
    }
    else if (strlen($color) > 20) {
        $_err['color'] = 'Maximum 20 characters';
    }

    // Validate: description
    if ($description == '') {
        $_err['description'] = 'Required';
    }
    else if (strlen($description) > 255) {
        $_err['description'] = 'Maximum 255 characters';
    }

    // Validate: unit_price
    if ($unit_price == '') {
        $_err['unit_price'] = 'Required';
    }
    else if (!is_money($unit_price)) {
        $_err['unit_price'] = 'Must be money';
    }
    else if ($unit_price < 0.01 || $unit_price > 9999.99) {
        $_err['unit_price'] = 'Must between 0.01 - 9999.99';
    }

    // Validate: stock_qty
    if ($stock_qty == '') {
        $_err['stock_qty'] = 'Required';
    }
    else if (!ctype_digit($stock_qty)) {
        $_err['stock_qty'] = 'Must be a whole number';
    }

    // Validate: status
    if (!in_array($status, ['In Stock', 'Out of Stock'])) {
        $_err['status'] = 'Required';
    }

    // Validate: category_id
    if (!key_exists($category_id, $categories)) {
        $_err['category_id'] = 'Required';
    }

    // Validate: photo (file) --> optional
    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        }
        else if ($f->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    }

    // DB operation
    if (!$_err) {
        // Delete old photo + save new one --> optional
        if ($f) {
            if ($photo && file_exists("../../prod_photos/$photo")) {
                unlink("../../photos/$photo");
            }
            $photo = save_photo($f, '../../prod_photos');
        }

        $stm = $_db->prepare('
            UPDATE product
            SET name = ?, color = ?, description = ?, unit_price = ?, stock_qty = ?, status = ?, photo = ?, category_id = ?
            WHERE id = ?
        ');
        $stm->execute([$name, $color, $description, $unit_price, $stock_qty, $status, $photo, $category_id, $id]);

        temp('info', 'Record updated');
        redirect('list.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Product | Update';
include '../../_head.php';
?>

<p>
    <button data-get="list.php">Back to List</button>
</p>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="id">Id</label>
    <b><?= $id ?></b>
    <span></span>

    <label for="category_id">Category</label>
    <?= html_select('category_id', $categories, null) ?>
    <?= err('category_id') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="color">Color</label>
    <?= html_text('color', 'maxlength="20"') ?>
    <?= err('color') ?>

    <label for="description">Description</label>
    <?= html_text('description', 'maxlength="255"') ?>
    <?= err('description') ?>

    <label for="unit_price">Unit Price</label>
    <?= html_number('unit_price', 0.01, 9999.99, 0.01) ?>
    <?= err('unit_price') ?>

    <label for="stock_qty">Stock Qty</label>
    <?= html_number('stock_qty', 0, 9999, 1) ?>
    <?= err('stock_qty') ?>

    <label for="status">Status</label>
    <?= html_select('status', ['In Stock' => 'In Stock', 'Out of Stock' => 'Out of Stock'], null) ?>
    <?= err('status') ?>

    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/photos/<?= $photo ?>">
    </label>
    <?= err('photo') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../../_foot.php';