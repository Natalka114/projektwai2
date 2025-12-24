<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function get_db()
{
$mongo = new MongoDB\Client(
"mongodb://localhost:27017/wai"
,
[
'username' => 'wai_web'
,
'password' => 'w@i_w3b',
]);
$db = $mongo->wai;

return $db;
}
require_once __DIR__ . '/../../vendor/autoload.php';
try {
   
    $db = get_db();
    
   
    $Collection = $db->users;
} catch (Exception $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}
?>
