<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
if(!$is_logged_in){
    header("Location: login.php");
    exit();
}
?>
<html>
<head>
    <title>Profil użytkownika</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Profil</h1>
    
 <?php if(!empty($_SESSION['zdjecie'])): ?>
    <img src="ProfilesFoto/<?php echo htmlspecialchars($_SESSION['zdjecie']); ?>" 
         alt="Zdjęcie profilowe" > <?php endif; ?>

    <p>Witaj, <strong><?php echo htmlspecialchars($_SESSION['imie']); ?></strong>!</p>  ?></p>
    
    <hr>
    <p><a href="upload.php">Prześlij zdjęcie do galerii</a></p>
    <p><a href="gallery.php">Galeria zdjęć</a></p>
    <p><a href="logout.php">Wyloguj się</a></p>
</body>
</html>