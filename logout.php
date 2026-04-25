<?php
session_start();
// Destroy session and redirect
session_destroy();
header("Location: login.php");
exit();
?>