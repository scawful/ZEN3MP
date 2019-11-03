<?php
session_start();

//Clear Session
$_SESSION["user_login"] = "";
session_destroy();

// clear cookies
$utils->clearAuthCookie();

header("Location: ../../index.php")
 ?>
