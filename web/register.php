<?php
session_start();
require 'db.php'; 

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  $login = $_POST['login'];
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];
    $imie =$_POST['imie'];
    $nazwisko = $_POST['nazwisko'];

  
    if ($password !== $passwordConfirm) {
        $message = "Hasła nie są identyczne!";
    } else {
      
        $existingUser = $Collection->findOne(['login' => $login]);
        
        if ($existingUser) {
            $message = "Podany login jest już zajęty!";
        } else {
            $zdj = "default.png"; 

          
            if (isset($_FILES['zdjecie']) && $_FILES['zdjecie']['error'] == 0) {
                $uploadDir = 'ProfilesFoto/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                                
                $extension = strtolower(pathinfo($_FILES['zdjecie']['name'], PATHINFO_EXTENSION));
                $filename = uniqid() . '.' . $extension;
                $uploadFile = $uploadDir . $filename;
                
                $tmpName = $_FILES['zdjecie']['tmp_name'];
                list($width, $height) = getimagesize($tmpName);
                
             
                $newWidth = 150;
                $newHeight = 150;
                $thumb = imagecreatetruecolor($newWidth, $newHeight);

             
                $source = null;
                if ($extension == 'jpg' || $extension == 'jpeg') $source = imagecreatefromjpeg($tmpName);
                elseif ($extension == 'png') $source = imagecreatefrompng($tmpName);
                

                if ($source) {
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    if ($extension == 'jpg' || $extension == 'jpeg') imagejpeg($thumb, $uploadFile, 90);
                    elseif ($extension == 'png') imagepng($thumb, $uploadFile);
                    elseif ($extension == 'gif') imagegif($thumb, $uploadFile);
                    
                    $zdj = $filename; 
                    imagedestroy($thumb);
                    imagedestroy($source);
                }
            }


            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $result = $Collection->insertOne([
                    'email' => $email,
                    'login' => $login,
                    'password' => $hashedPassword,
                    'imie' => $imie,
                    'nazwisko' => $nazwisko,
                    'zdjecie' => $zdj
                ]);

                if ($result->getInsertedCount() == 1) {
                    $message = "Rejestracja udana! <a href='login.php'>Zaloguj się</a>";
                }
            } catch (Exception $e) {
                $message = "Błąd bazy danych: " . $e->getMessage();
            }
        }
    }
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
      <label>Login:</label><input type="login" name="login" required><br>
        <label>Hasło:</label><input type="password" name="password" required><br>
        <label>Potwierdź hasło:</label><input type="password" name="password_confirm" required><br>
        <label>Imię:</label><input type="text" name="imie" required><br>
        <label>Nazwisko:</label><input type="text" name="nazwisko" required><br>
        <label>Zdjęcie profilowe:</label><input type="file" name="zdjecie" accept="image/*" required><br><br> 
        <button type="submit">Zarejestruj się</button>
    </form>
</body>
</html>