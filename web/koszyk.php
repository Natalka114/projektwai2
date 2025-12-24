<?php
session_start();
require_once 'db.php'; 
$db = get_db();
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
 
    if ($_POST['action'] === 'save_selected') {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $selected = $_POST['selected_photos'] ?? [];
        $quantities = $_POST['quantities'] ?? [];
        foreach ($selected as $id) {
            $qty = isset($quantities[$id]) ? (int)$quantities[$id] : 1;
            $_SESSION['cart'][$id] = $qty;
        }
    }
    
    if ($_POST['action'] === 'remove_item' && isset($_POST['id'])) {
        $id_to_remove = $_POST['id'];
        if (isset($_SESSION['cart'][$id_to_remove])) {
            $_SESSION['cart'][$id_to_remove]--;
            if ($_SESSION['cart'][$id_to_remove] <= 0) {
                unset($_SESSION['cart'][$id_to_remove]);
            }
        }
    }

    if ($_POST['action'] === 'clear_cart') {
        unset($_SESSION['cart']);
    }

  
    if ($user_id) {
        $db->users->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($user_id)],
            ['$set' => ['saved_cart' => $_SESSION['cart'] ?? []]]
        );
    }
}

$referer = $_SERVER['HTTP_REFERER'] ?? 'gallery.php';
header("Location: " . $referer);
exit;