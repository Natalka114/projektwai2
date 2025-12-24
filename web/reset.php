<?php
require_once 'db.php';
$db = get_db();


$db->photos->deleteMany([]); 
$Collection->deleteMany([]); 

echo "Baza danych została wyczyszczona.<br>";


function deleteFilesFromDir($dir) {
    if (!is_dir($dir)) return;
    $files = glob($dir . '/*'); 
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'default.png') {
            unlink($file); 
        }
    }
}


deleteFilesFromDir(__DIR__ . '/images');


deleteFilesFromDir(__DIR__ . '/ProfilesFoto');

echo "Pliki z folderów images/ oraz ProfilesFoto/ zostały usunięte.<br>";
echo "<a href='index.php'>Wróć do strony głównej</a>";
?>