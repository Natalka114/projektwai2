<?php
session_start();
require 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];

    if ($password !== $passwordConfirm) {
        $message = "Hasła nie są zgodne!";
    } else {


    
    $existingUser = $collection->findOne(['email' => $email]);

    if ($existingUser) {
        $message = "Ten email jest już zajęty!";
    } else {
       
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

       
        $result = $collection->insertOne([
            'email' => $email,
            'password' => $passwordHash,
            'imie' => $imie,
            'nazwisko' => $nazwisko,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        if ($result->getInsertedCount() == 1) {
            $message = "Rejestracja udana! <a href='login.php'>Zaloguj się</a>";
        } else {
            $message = "Wystąpił błąd podczas rejestracji.";
        }
    }
}}
?>

<!DOCTYPE html>
<html lang="pl">
<head><title>Rejestracja</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Rejestracja</h2>
    <?php if($message): ?><p><strong><?= $message ?></strong></p><?php endif; ?>
    
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Hasło:</label><br>
        <input type="password" name="password" required><br><br>
        <label>Potwierdź hasło:</label><br>
        <input type="password" name="password_confirm" required><br><br>
        <label>Imię:</label><br>
        <input type="text" name="imie" required><br><br>
        <label>Nazwisko:</label><br>
        <input type="text" name="nazwisko" required><br><br>
        <button type="submit">Zarejestruj się</button>
    </form>
</body>
</html>