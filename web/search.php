<?php
session_start();
require_once 'db.php';
$user_id = $_SESSION['user_id'] ?? null;
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wyszukiwarka - ChessBook</title>
    <link rel="stylesheet" href="style.css">
    <style>
      
        .search-container { text-align: center; margin: 30px 0; }
        #search-input { padding: 10px; width: 300px; border-radius: 4px; border: 1px solid #ccc; font-size: 16px; }
        .gallery-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; padding: 20px; }
        .photo-card { border: 1px solid #ccc; padding: 10px; width: 220px; text-align: center; background: #fff; border-radius: 8px; }
        .photo-card img { width: 200px; height: 125px; object-fit: cover; border-radius: 5px; }
        
        .header-nav { background: #333; color: #fff; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; align-items: center; gap: 15px; }
        .cart-status { position: relative; display: inline-block; text-decoration: none; color: white; margin-right: 15px; }
        .cart-badge { background-color: #ff4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; position: absolute; top: -10px; right: -15px; font-weight: bold; }
        .profile-pic { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #4CAF50; background: #eee; }
    </style>
</head>
<body>

<div class="header-nav">
    <div class="nav-links">
        <a href="index.php" style="color:white; text-decoration:none;">🏠 Strona Główna</a>
        <a href="gallery.php" style="color:white; text-decoration:none;">🖼️ Galeria</a>
        <a href="cart.php" class="cart-status">
            🛒 Koszyk
            <?php if ($cart_count > 0): ?>
                <span class="cart-badge"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>
    </div>

    <div class="user-section">
        <?php if ($user_id): ?>
            <img src="ProfilesFoto/<?= htmlspecialchars($_SESSION['zdjecie'] ?? 'default-avatar.png') ?>" alt="Profilowe" class="profile-pic">
            <span style="color: white;">Witaj, <strong><?= htmlspecialchars($_SESSION['imie'] ?? 'Użytkowniku') ?></strong></span>
        <?php endif; ?>
    </div>
</div>

<div class="search-container">
    <h1>Wyszukiwarka zdjęć</h1>
    <input type="text" id="search-input" placeholder="Wpisz tytuł zdjęcia..." onkeyup="searchPhotos(this.value)">
</div>

<div id="results" class="gallery-container">
    <p style="color: #666;">Zacznij pisać, aby wyszukać zdjęcia...</p>
</div>

<script>
function searchPhotos(query) {
    const resultsDiv = document.getElementById('results');
    
   
    if (query.length === 0) {
        resultsDiv.innerHTML = '<p style="color: #666;">Zacznij pisać, aby wyszukać zdjęcia...</p>';
        return;
    }

   
    fetch('search_back_end.php?q=' + encodeURIComponent(query))
        .then(response => response.text())
        .then(html => {
            resultsDiv.innerHTML = html;
        })
        .catch(error => {
            console.error('Błąd:', error);
            resultsDiv.innerHTML = 'Wystąpił błąd podczas wyszukiwania.';
        });
}
</script>

</body>
</html>