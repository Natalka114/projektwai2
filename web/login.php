<?php
session_start();
require 'db.php';

$message = '';
$login_success = false;


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SESSION['user_id'])) {
    
    $login = $_POST['login'];
    $password = $_POST['password'] ?? '';

    if (!empty($login)) {
        $user = $Collection->findOne(['login' => $login]);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (string)$user['_id'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['zdjecie'] = $user['zdjecie'];
            $_SESSION['imie'] = $user['imie'];
            $_SESSION['nazwisko'] = $user['nazwisko'];
$_SESSION['cart'] = (array)($user['saved_cart'] ?? []);
            $login_success = true;
            $message = "Zalogowano pomyślnie!";
        } else {
            $message = "Błędny login lub hasło.";
        }
    } else {
        $message = "Proszę podać login.";
    }
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
           <p> twoj login: <strong><?= htmlspecialchars($_SESSION['login']) ?></strong></p>
            <img src="ProfilesFoto/<?= htmlspecialchars($_SESSION['zdjecie']) ?>" alt="Profilowe" style="width:100px;"><br>
            <a href="?logout=1">Wyloguj się</a>
            <p><a href="index.php">Przejdź do strony głównej</a></p>
            <p><a href="upload.php">Dodaj zdjecie</a></p>
            <p> <a href="gallery.php">Galeria</a></p>
        </div>
        <?php if($login_success): ?><p style="color:green"><?= $message ?></p><?php endif; ?>

    <?php else: ?>
        <?php if($message): ?><p style="color:red"><?= $message ?></p><?php endif; ?>

        <form method="POST">
            <label>login:</label><br>
            <input type="login" name="login" required><br><br>
            
            <label>Hasło:</label><br>
            <input type="password" name="password" required><br><br>
            
            <button type="submit">Zaloguj się</button>
        </form>
        <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
        <p><a href="index.php">Powrót do strony głównej</a></p>
    <?php endif; ?>
</body>
</html>