<?php
session_start();
require_once 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    
    $author = isset($_POST['author']) ? trim($_POST['author']) : 'Anonim';
    $title = isset($_POST['title']) ? trim($_POST['title']) : 'Bez tytułu';
    $visibility = 'public';
    if ($is_logged_in && isset($_POST['is_private'])) {
        $visibility = 'private';
    }

    $uploadDir = __DIR__ . '/images/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); 
    }

    $maxSize = 1 * 1024 * 1024; 
    $allowedTypes = ['image/jpeg', 'image/png'];
  
if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
    $errors[] = "Nie wybrano pliku lub plik jest uszkodzony.";
} else {
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    
    
    $mimeType = $finfo->file($file['tmp_name']);
}
    $errors = [];

    if (!in_array($mimeType, $allowedTypes)) {
        $errors[] = "Niedozwolony format pliku! Wybierz JPG lub PNG.";
    }

    if ($file['size'] > $maxSize) {
        $errors[] = "Plik jest za duży! Maksymalny rozmiar to 1 MB.";
    }

    if (!empty($errors)) {
        $message = implode("<br>", $errors);
    } else {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uniqueId = uniqid(); 
        $newFileName = $uniqueId . '.' . $extension;
        $targetPath = $uploadDir . $newFileName;
        
        $thumbFileName = 'thumb_' . $uniqueId . '.' . $extension;
        $thumbPath = $uploadDir . $thumbFileName;

        $db = get_db();

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $thumbWidth = 200;
            $thumbHeight = 125;
            
            list($origWidth, $origHeight) = getimagesize($targetPath);
            
            $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
            $source = null;

            if ($mimeType === 'image/jpeg') {
                $source = imagecreatefromjpeg($targetPath);
            } elseif ($mimeType === 'image/png') {
                $source = imagecreatefrompng($targetPath);
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
            }

            if ($source) {
                imagecopyresampled(
                    $thumb, 
                    $source, 
                    0, 0, 0, 0, 
                    (int)$thumbWidth, 
                    (int)$thumbHeight, 
                    (int)$origWidth, 
                    (int)$origHeight
                );

                if ($mimeType === 'image/jpeg') {
                    imagejpeg($thumb, $thumbPath, 90);
                } elseif ($mimeType === 'image/png') {
                    imagepng($thumb, $thumbPath);
                }

                imagedestroy($thumb);
                imagedestroy($source);
            }
          
            $document = [
                'filename' => $newFileName,
                'thumbnail' => $thumbFileName, 
                'original_name' => $file['name'],
                'mime_type' => $mimeType,
                'author' => $author,
                'title' => $title,
                'visibility' => $visibility,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ];

            $document['user_id'] = $is_logged_in ? $_SESSION['user_id'] : null;

            $db->photos->insertOne($document);
            $message = "Zdjęcie i miniatura zostały zapisane pomyślnie!";
        } else {
            $message = "Wystąpił błąd podczas zapisywania pliku na serwerze.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Prześlij zdjęcie</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Dodaj zdjęcie do galerii</h2>
    
    <a href="index.php">Powrót do strony głównej</a>
    <hr>

    <?php if($message): ?>
        <p style="background-color: #f0f0f0; padding: 10px;"><strong><?= $message ?></strong></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Wybierz zdjęcie (JPG/PNG, max 1MB):</label><br>
        <input type="file" name="image" required><br><br>
        
        <label>Autor:</label><br>
        <input type="text" name="author" placeholder="Twoje imię/nick" value="<?= $is_logged_in && isset($_SESSION['imie']) ? htmlspecialchars($_SESSION['imie']) : '' ?>"><br><br>
        
        <label>Tytuł zdjęcia:</label><br>
        <input type="text" name="title" placeholder="Tytuł"><br><br>    

        <?php if ($is_logged_in): ?>
            <div style="background-color: #eef; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <label>
                    <input type="checkbox" name="is_private" value="1"> 
                    <strong>Ustaw jako prywatne</strong> (widoczne tylko dla Ciebie)
                </label>
            </div>
        <?php else: ?>
            <p><em><small>Zaloguj się, aby móc dodawać zdjęcia prywatne.</small></em></p>
        <?php endif; ?>

        <button type="submit">Wyślij na serwer</button>
        <a href="gallery.php" style="margin-left: 20px;">Zobacz galerię</a>
    </form>
</body>
</html>