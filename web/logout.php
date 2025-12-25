<?php
session_start();
session_destroy();
$_SESSION["logged_out"] = true;
session_regenerate_id(true);

header("Location: login.php");
exit;
?>