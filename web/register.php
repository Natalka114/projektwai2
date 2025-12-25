<?php
session_start();
require 'db.php'; 
require 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = registerUser($Collection, $_POST, $_FILES);
    $message = $result['message'];
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Rejestracja</title>
</head>
<body>
    <h1>Rejestracja użytkownika</h1>
    <?php if($message): ?><p style="color:blue"><?= $message ?></p><?php endif; ?>

    <form method="POST" enctype="multipart/form-data"> 
        <label>Email:</label><input type="email" name="email" required><br>
        <label>Login:</label><input type="text" name="login" required><br>
        <label>Hasło:</label><input type="password" name="password" required><br>
        <label>Potwierdź hasło:</label><input type="password" name="password_confirm" required><br>
        <label>Imię:</label><input type="text" name="imie" required><br>
        <label>Nazwisko:</label><input type="text" name="nazwisko" required><br>
        <label>Zdjęcie profilowe:</label><input type="file" name="zdjecie" accept="image/*" required><br><br> 
        <button type="submit">Zarejestruj się</button>
    </form>
</body>
</html>