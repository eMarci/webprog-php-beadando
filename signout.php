<?php
include_once "classes/auth.php";

session_start();
$auth = new Auth();
$auth->logout();
header("Location: index.php");
exit();
?>