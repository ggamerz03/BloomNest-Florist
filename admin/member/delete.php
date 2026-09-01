<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Admin only
auth('Admin');

if (is_post()) {
    $id = req('id');

    // Check the role of the target user
    $stm = $_db->prepare('SELECT role FROM user WHERE id = ?');
    $stm->execute([$id]);
    $target_role = $stm->fetchColumn();

    // Count how many admins currently exist
    $stm = $_db->prepare("SELECT COUNT(*) FROM user WHERE role = 'Admin'");
    $stm->execute();
    $admin_count = $stm->fetchColumn();

    // Block deletion if this is the last remaining admin
    if ($target_role == 'Admin' && $admin_count <= 1) {
        temp('info', 'Cannot be deleted: at least one admin is required');
        redirect('list.php');
    }

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