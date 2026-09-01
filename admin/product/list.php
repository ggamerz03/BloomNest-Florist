<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

// (1) Search & Filter
$keyword     = req('keyword', '');
$category_id = req('category_id', '');
$stock       = req('stock', '');

// (2) Sorting
$fields = [
    'p.category_id' => 'Category',
    'p.id'          => 'Id',
    'p.name'        => 'Name',
    'p.color'       => 'Color',
    'p.description' => 'Description',
    'p.unit_price'  => 'Unit Price',
    'p.stock_qty'   => 'Stock Qty',
    'p.status'      => 'Status',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'p.id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// (3) Paging
$page = req('page', 1);

require_once '../../lib/SimplePager.php';

// Categories for the filter dropdown (id => name)
$categories = $_db->query('SELECT id, name FROM category ORDER BY name')->fetchAll(PDO::FETCH_KEY_PAIR);

$query = "SELECT p.*, c.name AS category_name FROM product p
          JOIN category c ON p.category_id = c.id
          WHERE (p.name LIKE ? OR p.id LIKE ?)
          AND (p.category_id = ? OR ?)
          AND (
              ? = ''
              OR (? = 'in' AND p.stock_qty > 0)
              OR (? = 'out' AND p.stock_qty <= 0)
          )
          ORDER BY $sort $dir";

$params = ["%$keyword%", "%$keyword%", $category_id, $category_id == '', $stock, $stock, $stock];

$p = new SimplePager($query, $params, 10, $page);
$arr = $p->result;

// Query string used to keep the current search/filter state on sort & pager links
$qs = 'keyword=' . urlencode($keyword) . "&category_id=$category_id&stock=$stock";

// ----------------------------------------------------------------------------

$_title = 'Product | Listing';
include '../../_head.php';
?>

<style>
    .popup {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
</style>

<p>
    <button data-get="insert.php">Insert Product</button>
</p>

<form class="form">
    <label for="keyword">Keyword</label>
    <?= html_search('keyword', 'placeholder="Product Name / Id"') ?>
    <span></span>

    <label for="category_id">Category</label>
    <?= html_select('category_id', $categories, 'All') ?>
    <span></span>

    <label for="stock">Stock</label>
    <?= html_select('stock', ['in' => 'In Stock', 'out' => 'Out of Stock'], 'All') ?>
    <span></span>

    <input type="hidden" name="sort" value="<?= $sort ?>">
    <input type="hidden" name="dir" value="<?= $dir ?>">

    <section>
        <button>Search</button>
    </section>
</form>

<p>
    <?= $p->count ?> of <?= $p->item_count ?> record(s) |
    Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

<table class="table">
    <tr>
        <?= table_headers($fields, $sort, $dir, "$qs&page=$page") ?>
        <th></th>
    </tr>

    <?php foreach ($arr as $row): ?>
    <tr data-get="detail.php?id=<?= $row->id ?>">
        <td><?= encode($row->category_name) ?></td>
        <td><?= $row->id ?></td>
        <td><?= encode($row->name) ?></td>
        <td><?= encode($row->color) ?></td>
        <td><?= encode($row->description) ?></td>
        <td><?= number_format($row->unit_price, 2) ?></td>
        <td><?= $row->stock_qty ?></td>
        <td><?= $row->stock_qty > 0 ? 'In Stock' : 'Out of Stock' ?></td>
        <td>
            <button data-get="update.php?id=<?= $row->id ?>">Update</button>
            <img src="/prod_photos/<?= $row->photo ?>" class="popup">
        </td>
    </tr>
    <?php endforeach ?>
</table>

<br>

<?= $p->html("sort=$sort&dir=$dir&$qs") ?>

<?php
include '../../_foot.php';