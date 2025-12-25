<?php
session_start();
require_once 'db.php';

$db = get_db();
$user_id = $_SESSION['user_id'] ?? null;


$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 4; 
$skip = ($page - 1) * $limit;

$query = [
    '$or' => [
        ['visibility' => 'public'],
        ['user_id' => $user_id]
    ]   
];

$totalPhotos = $db->photos->countDocuments($query);
$totalPages = ceil($totalPhotos / $limit);

$cursor = $db->photos->find($query, [
    'sort' => ['created_at' => -1],
    'limit' => $limit,
    'skip' => $skip
]);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Galeria ChessBook</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .gallery-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; padding: 20px; }
        .photo-card { border: 1px solid #ccc; padding: 10px; width: 220px; text-align: center; background: #fff; border-radius: 8px; }
        .photo-card img { width: 200px; height: 125px; object-fit: cover; border-radius: 5px; }
        .pagination { text-align: center; margin: 20px; }
        .pagination a { padding: 8px 16px; border: 1px solid #ddd; text-decoration: none; margin: 0 4px; border-radius: 4px; }
        .pagination .active { background-color: #4CAF50; color: white; border: 1px solid #4CAF50; }
        
   
        .header-nav { background: #333; color: #fff; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; align-items: center; gap: 15px; }
        .user-section { display: flex; align-items: center; gap: 10px; }
        
        .cart-status { position: relative; display: inline-block; text-decoration: none; color: white; margin-right: 15px; }
        .cart-badge { background-color: #ff4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; position: absolute; top: -10px; right: -15px; font-weight: bold; }
        
     
        .profile-pic { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #4CAF50; background: #eee; }
        
        .qty-input { width: 50px; margin-top: 5px; text-align: center; }
    </style>
</head>
<body>

<div class="header-nav">
    <div class="nav-links">
        <a href="index.php" style="color:white; text-decoration:none;">🏠 Strona Główna</a>
        <a href="upload.php" style="color:white; text-decoration:none;">📤 Dodaj zdjęcie</a>
        <a href="search.php" style="color:white; text-decoration:none;">🔍 Wyszukiwarka</a>
        <a href="cart.php" class="cart-status">
            🛒 Koszyk
            <?php if ($cart_count > 0): ?>
                <span class="cart-badge"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>
    </div>

    <div class="user-section">
        <?php if ($user_id): ?>
            <?php 
                $zdjecie = $_SESSION['zdjecie'] ?? 'images/default-avatar.png'; 
            ?>
           <img src="ProfilesFoto/<?= htmlspecialchars($_SESSION['zdjecie']) ?>" alt="Profilowe" width="35" height="35" class="profile-pic"><br>
            
            <span>Witaj, <strong><?= htmlspecialchars($_SESSION['imie'] ?? 'Użytkowniku') ?></strong></span>
            <a href="login.php?logout=1" style="color:#ff6666; margin-left:10px; text-decoration:none; font-size: 14px;">Wyloguj się</a>
        <?php else: ?>
            <a href="login.php" style="color:white; text-decoration:none;">Zaloguj się</a>
        <?php endif; ?>
    </div>
</div>

<h1 style="text-align: center; margin-top: 30px;">Galeria Zdjęć</h1>
<p style="text-align: center; color: #666;">UWAGA! zaaktualizuj koszyk przed przejsciem na kolejna strone ze zdjeciami!</p>

<form action="koszyk.php" method="POST">
    <input type="hidden" name="action" value="save_selected">
    
    <div style="text-align: center; margin-bottom: 20px;">
        <button type="submit" style="padding: 10px 20px; cursor: pointer; background: #4CAF50; color: white; border: none; border-radius: 4px;">
            Zaktualizuj / Dodaj do koszyka
        </button>
    </div>

    <div class="gallery-container">
        <?php foreach ($cursor as $photo): 
            $photo_id = (string)$photo['_id'];
            $thumb = !empty($photo['thumbnail']) ? $photo['thumbnail'] : $photo['filename'];
            
            $is_checked = isset($_SESSION['cart'][$photo_id]) ? 'checked' : '';
            $current_qty = $_SESSION['cart'][$photo_id] ?? 1;
        ?>
            <div class="photo-card" style="<?= $is_checked ? 'background-color: #f0fff0; border-color: #4CAF50;' : '' ?>">
                <input type="checkbox" name="selected_photos[]" value="<?= $photo_id ?>" <?= $is_checked ?>>
                <br>
                
                <label style="font-size: 12px;">Ilość:</label>
                <input type="number" name="quantities[<?= $photo_id ?>]" 
                       value="<?= $is_checked ? $current_qty : 1 ?>" 
                       min="1" class="qty-input">
                <br><br>

                <a href="images/<?= $photo['filename'] ?>" target="_blank">
                    <img src="images/<?= $thumb ?>" alt="Miniatura">
                </a>
               <div class="photo-info">
    <h4 style="margin: 10px 0 5px 0;"><?= htmlspecialchars($photo['title'] ?? 'Bez tytułu') ?></h4>
    <small>Autor: <?= htmlspecialchars($photo['author'] ?? 'Anonim') ?></small>
    
    <?php if (isset($photo['visibility']) && $photo['visibility'] === 'private'): ?>
        <br>
        <span style="color: #d9534f; font-size: 10px; font-weight: bold; text-transform: uppercase;">
            🔒 Prywatne
        </span>
    <?php endif; ?>
</div>
            </div>
        <?php endforeach; ?>
    </div>
</form>

<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= $page === $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>

</body>
</html>