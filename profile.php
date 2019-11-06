<?php
require('src/config.php');
include("src/classes/User.php");
include("src/classes/Post.php");
include("src/classes/Character.php");
include("src/classes/Message.php");
include("src/classes/Notification.php");
include("src/classes/Inventory.php");
include("src/classes/Quest.php");
include("src/forms/settings_form.php");

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

include("src/auth.php");
include("src/globals.php");

if(isset($_GET['profile_username'])) {
  $username = $_GET['profile_username'];
  $user_details_query = $spdo->prepare('SELECT * FROM users WHERE username = ?');
  $user_details_query->execute([$username]);
  $user_array = $user_details_query->fetch();
  $prof_num_friends = (substr_count($user_array['friend_list'], ",")) - 1;
}

if(isset($_POST['remove_friend'])) {
  $user = new User($userLoggedIn, $spdo);
  $user->removeFriend($username);
}

if(isset($_POST['add_friend'])) {
  $user = new User($userLoggedIn, $spdo);
  $user->sendRequest($username);
}

if(isset($_POST['respond_request'])) {
  header("Location: requests.php");
}

if(isset($_POST['post_message'])) {
  if(isset($_POST['message_body'])) {
    $body = mysqli_real_escape_string($connect_social, $_POST['message_body']);
    $date = date("Y-m-d H:i:s");
    $message_obj->sendMessage($username, $body, $date);
  }
  $link = '#profileTabs a[href="#messages_div"]';
  echo "<script>
          $(function() {
              $('" . $link . "').tab('show');
          });
        </script>";
}

$message_obj = new Message($connect_social, $userLoggedIn, $spdo);
$profile_user_obj = new User($username, $spdo);
$user_obj = new User($userLoggedIn, $spdo);
$character_obj = new Character($username, $spdo, $rpdo);

$profile_uid = $profile_user_obj->getUserID();
$settings_id = $user_obj->getUserID();

$about_stmt = $spdo->prepare('SELECT * FROM users_about WHERE user_id = ?');
$about_stmt->execute([$settings_id]);
$user_about = $about_stmt->fetch();

$twig->addGlobal('user_array', $user_array);
$twig->addGlobal('username', $username);
$twig->addGlobal('profile_user_obj', $profile_user_obj);
$twig->addGlobal('profile_uid', $profile_uid);
$twig->addGlobal('char_obj', $character_obj);
$twig->addGlobal('num_friends', $prof_num_friends);
$twig->addGlobal('user', $user_obj);
$twig->addGlobal('user_about', $user_about);

echo $twig->render('profile.twig');

?>
