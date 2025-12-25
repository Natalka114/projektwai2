<?php

function processProfileImage($file) {
    $uploadDir = 'ProfilesFoto/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $extension;
    $uploadFile = $uploadDir . $filename;
    
    $tmpName = $file['tmp_name'];
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
        
        imagedestroy($thumb);
        imagedestroy($source);
        return $filename;
    }
    return "default.png";
}

function registerUser($Collection, $data, $file) {
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $login = $data['login'];
    $password = $data['password'];
    $passwordConfirm = $data['password_confirm'];
    $imie = $data['imie'];
    $nazwisko = $data['nazwisko'];

    if ($password !== $passwordConfirm) {
        return ['success' => false, 'message' => "Hasła nie są identyczne!"];
    }

    $existingUser = $Collection->findOne(['login' => $login]);
    if ($existingUser) {
        return ['success' => false, 'message' => "Podany login jest już zajęty!"];
    }

    $zdj = "default.png";
    if (isset($file['zdjecie']) && $file['zdjecie']['error'] == 0) {
        $zdj = processProfileImage($file['zdjecie']);
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
            return ['success' => true, 'message' => "Rejestracja udana! <a href='login.php'>Zaloguj się</a>"];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Błąd bazy danych: " . $e->getMessage()];
    }
}

function loginUser($Collection, $login, $password) {
    if (empty($login)) {
        return ['success' => false, 'message' => "Proszę podać login."];
    }

    $user = $Collection->findOne(['login' => $login]);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = (string)$user['_id'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['zdjecie'] = $user['zdjecie'];
        $_SESSION['imie'] = $user['imie'];
        $_SESSION['nazwisko'] = $user['nazwisko'];
        $_SESSION['cart'] = (array)($user['saved_cart'] ?? []);
        return ['success' => true, 'message' => "Zalogowano pomyślnie!"];
    }

    return ['success' => false, 'message' => "Błędny login lub hasło."];
}


function logoutUser() {
    session_destroy();
    session_regenerate_id(true);
    setcookie("PHPSESSID","", time() -0,"/");
    
}