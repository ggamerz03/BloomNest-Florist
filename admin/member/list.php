<?php
require '../../_base.php';
//-----------------------------------------------------------------------------



// ----------------------------------------------------------------------------
$_title = 'Member List';
include '../../_head.php';
?>

<form>
    <?= html_search('name') ?>
    <?= html_select('program_id', $_programs, 'All') ?>
    <button>Search</button>
</form>

<table class="table">
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Email</th>
        <th>Address</th>
        <th>Phone Number</th>
        <th>Role</th>
    </tr>

    <?php foreach ($arr as $s): ?>
    <tr>
        <td><?= $s->id ?></td>
        <td><?= $s->name ?></td>
        <td><?= $s->address ?></td>
        <td><?= $s->phone_number ?></td>
        <td><?= $s->role ?></td>
    </tr>
    <?php endforeach ?>
</table>


<?php
include '../../_foot.php';