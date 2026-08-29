<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

if (is_post()) {
    $id = req('id');

    // Delete photo
    $stm = $_db->prepare('SELECT profile_photo FROM user WHERE id = ?');
    $stm->execute([$id]);
    $photo = $stm->fetchColumn();

    if ($photo && file_exists("../../photos/$photo")) {
        unlink("../../photos/$photo");
    }

    $stm = $_db->prepare('DELETE FROM user WHERE id = ?');
    $stm->execute([$id]);
    temp('info', 'Record deleted');
}

redirect('list.php');

// ----------------------------------------------------------------------------