<?php

session_start();
logoutUser();
header("Location: login.php");
exit;
?>