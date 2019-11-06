<?php
// Get Current date, time
$current_time = time();
$current_date = date("Y-m-d H:i:s", $current_time);

// Set Cookie expiration for 1 month
$cookie_expiration_time = $current_time + (30 * 24 * 60 * 60);  // for 1 month

$isLoggedIn = false;
$userLoggedIn = "Guest";
$login_flag = 0;

if (! empty($_SESSION["username"])) {
    $userLoggedIn = $_SESSION['username'];
    $stmt = $spdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$userLoggedIn]);
    $user = $stmt->fetch();
    $num_friends = (substr_count($user['friend_list'], ",")) - 1;
    $isLoggedIn = true;
    $login_flag = 1;
}

// Check if loggedin session exists
else if (! empty($_COOKIE["user_login"]) && ! empty($_COOKIE["random_password"]) && ! empty($_COOKIE["random_selector"])) {
    // Initiate auth token verification diirective to false
    $isPasswordVerified = false;
    $isSelectorVerified = false;
    $isExpiryDateVerified = false;

    $user_auth = new User($userLoggedIn, $spdo);

    // Get token for username
    $userToken = $user_auth->getTokenByUser($_COOKIE["user_login"],0);

    // Validate random password cookie with database
    if (password_verify($_COOKIE["random_password"], $userToken["password_hash"])) {
        $isPasswordVerified = true;
    }

    // Validate random selector cookie with database
    if (password_verify($_COOKIE["random_selector"], $userToken["selector_hash"])) {
        $isSelectorVerified = true;
    }

    // check cookie expiration by date
    if($userToken["expiry_date"] >= $current_date) {
        $isExpiryDateVerified = true;
    }

    // Redirect if all cookie based validation retuens true
    // Else, mark the token as expired and clear cookies
    if (!empty($userToken["id"]) && $isPasswordVerified && $isSelectorVerified && $isExpiryDateVerified) {
        $isLoggedIn = true;
    } else {
        if(!empty($userToken["id"])) {
            $user_auth->markExpired($userToken["id"]);
        }
        // clear cookies
        $utils->clearAuthCookie();

    }
}
?>
