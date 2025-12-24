<?php
session_start();
require_once 'db.php';

$db = get_db();
$user_id = $_SESSION['user_id'] ?? null;


$q = $_GET['q'] ?? '';

if (empty($q)) {
    exit; 
}


$query = [
    '$and' => [
        ['title' => ['$regex' => $q, '$options' => 'i']],
        ['$or' => [
            ['visibility' => 'public'],
            ['user_id' => $user_id]
        ]]
    ]
];

$cursor = $db->photos->find($query, [
    'sort' => ['created_at' => -1]
]);

$resultsCount = 0;

foreach ($cursor as $photo) {
    $resultsCount++;
    $thumb = !empty($photo['thumbnail']) ? $photo['thumbnail'] : $photo['filename'];
    $title = htmlspecialchars($photo['title'] ?? 'Bez tytułu');
    $author = htmlspecialchars($photo['author'] ?? 'Anonim');
    $is_private = isset($photo['visibility']) && $photo['visibility'] === 'private';

   
    echo '<div class="photo-card">';
    echo '  <a href="images/' . $photo['filename'] . '" target="_blank">';
    echo '      <img src="images/' . $thumb . '" alt="Miniatura">';
    echo '  </a>';
    echo '  <div class="photo-info">';
    echo '      <h4 style="margin: 10px 0 5px 0;">' . $title . '</h4>';
    echo '      <small>Autor: ' . $author . '</small>';
    
    if ($is_private) {
        echo '<br><span style="color: #d9534f; font-size: 10px; font-weight: bold; text-transform: uppercase;">🔒 Prywatne</span>';
    }
    
    echo '  </div>';
    echo '</div>';
}

if ($resultsCount === 0) {
    echo '<p style="color: #999;">Nie znaleziono zdjęć pasujących do frazy: <strong>' . htmlspecialchars($q) . '</strong></p>';
}