<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare("UPDATE orders SET status = 'Completed' WHERE id = ?");
    $stm->execute([$id]);

    temp('info', 'Order completed');
}

redirect('list.php');