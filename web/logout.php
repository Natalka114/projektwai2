<?php
require 'functions.php';
require 'db.php';
session_start();
logoutUser();
header("Location: login.php");
exit;
?>