<?php
namespace zen3mp;
$message_obj = new Message($userLoggedIn, $spdo);

if(isset($_POST['post_message'])) {

    if(isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($connect_social, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
    }

}
?>
