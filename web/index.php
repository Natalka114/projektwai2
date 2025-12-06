<?php
session_start();
require 'db.php';


$is_logged_in = isset($_SESSION['user_id']);


if ($is_logged_in && !isset($_SESSION['imie'])) {
    $user = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
    if ($user) {
        $_SESSION['imie'] = $user['imie'];
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChessBook</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if ($is_logged_in): ?>
        <h1>Witaj na ChessBook, <?= htmlspecialchars($_SESSION['imie']); ?>!</h1>
    <?php else: ?>
        <h1>Witaj na ChessBook!</h1>
    <?php endif; ?>

    <p>Witaj na stronie, na której możesz dodać zdjęcia ze swoich partii, turniejów lub zrobione Magnusowi Carlsenowi z ukrycia.</p>

    <a href="upload.php">Prześlij zdjęcie</a> |

    <?php if ($is_logged_in): ?>
        <a href="profile.php">Mój profil</a> |
        <a href="logout.php">Wyloguj się</a>
    <?php else: ?>
        <a href="login.php">Zaloguj się</a>
    <?php endif; ?>
</body>
</html>