<?php 
$message_obj = new Message($connect_social, $userLoggedIn, $spdo);

if(isset($_GET['u']))
    $user_to = $_GET['u'];
else {
    $user_to = $message_obj->getMostRecentUser();
    if($user_to == false)
        $user_to = 'new';
}

if(isset($_POST['post_message'])) {

    if(isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($connect_social, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
    }

}

if($user_to != "new") {
    $user_to_obj = new User($user_to, $spdo);
    echo "<div class='article'>";
    echo "<h4> You and <a href='$user_to'>" . $user_to_obj->getDisplayName() . "</a></h4><hr><br/>";
    echo "<div class='loaded_messages' id='scroll_messages'>";
        echo $message_obj->getMessages($user_to);
        echo "</div>";
} else {
    echo "<br>";
}
?>