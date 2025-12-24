<?php
session_start();
require_once 'db.php';

$db = get_db();
$cart_items = $_SESSION['cart'] ?? [];
$photos = [];

if (!empty($cart_items)) {
    $ids = array_keys($cart_items);
    $mongoIds = array_map(function($id) { return new MongoDB\BSON\ObjectId($id); }, $ids);
    $photos = $db->photos->find(['_id' => ['$in' => $mongoIds]]);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Twoje Wybrane Zdjęcia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color: #fcfad7;">

    <div style="text-align: center; padding: 20px;">
        <h1>Wybrane zdjęcia (sztuk: <?= array_sum($cart_items) ?>)</h1>
        <a href="gallery.php">Wróć do galerii</a>
        
        <form action="koszyk.php" method="POST" style="margin-top: 10px;">
            <input type="hidden" name="action" value="clear_cart">
            <button type="submit" style="cursor:pointer; padding: 5px 10px;">Wyczyść cały koszyk</button>
        </form>
    </div>

    <div style="display: flex; flex-direction: column; align-items: center; gap: 15px; padding: 20px;">
        
        <?php foreach ($photos as $photo): 
            $pid = (string)$photo['_id'];
        ?>
            <div style="display: flex; flex-direction: row; align-items: center; background: white; border: 1px solid #ccc; border-radius: 8px; padding: 10px; width: 100%; max-width: 650px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); position: relative;">
                
                <img src="images/<?= htmlspecialchars($photo['thumbnail']) ?>" alt="Foto" 
                     style="width: 120px !important; height: 80px !important; object-fit: cover; margin-right: 20px; border-radius: 4px; display: block;">

                <div style="flex-grow: 1; text-align: left;">
                    <p style="margin: 0; font-weight: bold; color: #333;"><?= htmlspecialchars($photo['title'] ?? 'Bez tytułu') ?></p>
                    <p style="margin: 5px 0 0 0; color: #666;">Ilość: <?= $cart_items[$pid] ?> szt.</p>
                </div>
                
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px; min-width: 100px;">
                    <a href="images/<?= $photo['filename'] ?>" target="_blank" style="font-size: 12px; color: #007bff; text-decoration: underline;">Powiększ</a>
                    
                    <form action="koszyk.php" method="POST" style="margin: 0; padding: 0; display: block; width: auto; position: static;">
                        <input type="hidden" name="action" value="remove_item">
                        <input type="hidden" name="id" value="<?= $pid ?>">
                        <button type="submit" >
                            Usuń 1 szt.
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($cart_items)): ?>
            <p>Koszyk jest pusty.</p>
        <?php endif; ?>
    </div>

</body>
</html>