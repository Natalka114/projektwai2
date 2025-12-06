<?php
session_start();
require_once 'db.php';

$db = get_db();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;



if ($user_id) {
 
    $query = [
        '$or' => [
            ['visibility' => 'public'],
            ['user_id' => $user_id]
        ]   
    ];
} else {
 
    $query = [
        'visibility' => 'public'
    ];
}


$cursor = $db->photos->find($query, [
    'sort' => ['created_at' => -1]
]);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeria ChessBook</title>
    <link rel="stylesheet" href="style.css">
    <style>
      
        .gallery-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }
        .photo-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            width: 300px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .photo-card img {
            width: 100%;
            height: 200px;
            object-fit: cover; 
            border-radius: 5px;
        }
        .photo-info {
            margin-top: 10px;
        }
        .badge-private {
            background-color: #ffcccc;
            color: #cc0000;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8em;
            float: right;
        }
    </style>
</head>
<body>

    <div style="text-align: center; margin-top: 20px;">
        <h1>Galeria Zdjęć</h1>
        <a href="index.php">Wróć do strony głównej</a> | 
        <a href="upload.php">Dodaj nowe zdjęcie</a>
    </div>

    <div class="gallery-container">
        <?php 
     
        $count = 0; 
        ?>

        <?php foreach ($cursor as $photo): ?>
            <?php $count++; ?>
            <div class="photo-card">
                <?php 
                  
                    $imagePath = 'images/' . $photo['filename']; 
                ?>
                
                <a href="<?= $imagePath ?>" target="_blank">
                    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($photo['title']) ?>">
                </a>
                
                <div class="photo-info">
                    <?php if (isset($photo['visibility']) && $photo['visibility'] === 'private'): ?>
                        <span class="badge-private">Prywatne</span>
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($photo['title'] ?? 'Bez tytułu') ?></h3>
                    <p>Autor: <strong><?= htmlspecialchars($photo['author'] ?? 'Nieznany') ?></strong></p>
                    
                    <?php if (isset($photo['created_at'])): ?>
                        <small>Data: <?= $photo['created_at']->toDateTime()->format('Y-m-d H:i') ?></small>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($count === 0): ?>
            <p>Brak zdjęć do wyświetlenia. Bądź pierwszy i dodaj coś!</p>
        <?php endif; ?>
    </div>

</body>
</html>