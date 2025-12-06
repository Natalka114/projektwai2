<?php
session_start();
require 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    
    $user = $collection->findOne(['email' => $email]);

    
    if ($user && password_verify($password, $user['password'])) {
       
        $_SESSION['user_id'] = (string)$user['_id'];
        $_SESSION['email'] = $user['email'];
        
        header("Location: index.php"); 
        exit;
    } else {
        $message = "Błędny email lub hasło.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head><title>Logowanie</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Logowanie</h2>
    <?php if($message): ?><p style="color:red"><?= $message ?></p><?php endif; ?>

    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Hasło:</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Zaloguj się</button>
    </form>
    <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
    <p><a href="index.php">Kontynuuj bez logowania</a></p>
</body>
</html>