<?php
if(isset($_GET['s']) == session_id()) {

    session_start();
    //Clear Session
    $_SESSION["user_login"] = "";
    session_destroy();

    // clear cookies
    $utils->clearAuthCookie();

    header("Location: ../../index.php");

} else {
    echo 'No';
}


?>
