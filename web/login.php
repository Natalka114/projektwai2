<?php
session_start();
require 'db.php';
require 'functions.php';

$message = '';
$login_success = false;

if (isset($_GET['logout'])) {
    logoutUser();
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SESSION['user_id'])) {
    $result = loginUser($Collection, $_POST['login'], $_POST['password'] ?? '');
    $message = $result['message'];
    $login_success = $result['success'];
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Mój profil</h2>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-info">
            <p>Witaj, <strong><?= htmlspecialchars($_SESSION['imie'] ?? 'Użytkowniku') ?></strong> <?= htmlspecialchars($_SESSION['login'] ?? '') ?>!</p>
            <p> twój login: <strong><?= htmlspecialchars($_SESSION['login']) ?></strong></p>
            <img src="ProfilesFoto/<?= htmlspecialchars($_SESSION['zdjecie']) ?>" alt="Profilowe" style="width:100px;"><br>
            <a href="logout.php">Wyloguj się</a>
            <p><a href="index.php">Przejdź do strony głównej</a></p>
            <p><a href="upload.php">Dodaj zdjęcie</a></p>
            <p><a href="gallery.php">Galeria</a></p>
        </div>
        <?php if($login_success): ?><p style="color:green"><?= $message ?></p><?php endif; ?>

    <?php else: ?>
        <?php if($message): ?><p style="color:red"><?= $message ?></p><?php endif; ?>

        <form method="POST">
            <label>login:</label><br>
            <input type="text" name="login" required><br><br>
            
            <label>Hasło:</label><br>
            <input type="password" name="password" required><br><br>
            
            <button type="submit">Zaloguj się</button>
        </form>
        <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
        <p><a href="index.php">Powrót do strony głównej</a></p>
    <?php endif; ?>
</body>
</html>