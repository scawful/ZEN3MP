<?php
namespace zen3mp;

require("src/config.php");
include("src/classes/Utils.php");
include("src/classes/User.php");
include("src/classes/Post.php");
include("src/classes/Character.php");
include("src/classes/Message.php");
include("src/classes/Notification.php");
include("src/classes/Inventory.php");
include("src/classes/Quest.php");
require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);
$utils = new Utils();

include("src/auth.php");
include("src/globals.php");

function listRequests($spdo, $userLoggedIn) {

    $stmt = $spdo->prepare('SELECT * FROM friend_requests WHERE user_to = ?');
    $stmt->execute([$userLoggedIn]);

    if($stmt->rowCount() == 0) {
        echo "You have no friend requests at this time.";
    } else {

        while($row = $stmt->fetch()) {
            $user_from = $row['user_from'];
            $user_from_obj = new User($user_from, $spdo);

            echo $user_from_obj->getUsername() . " sent you a friend request!";
            $user_from_friend_array = $user_from_obj->getFriendList();

            if(isset($_POST['accept_request' . $user_from ])) {
                $add_friend = $spdo->prepare('UPDATE users SET friend_list=CONCAT(friend_list, ?) WHERE username = ?');
                $add_friend->execute(["$user_from,", $userLoggedIn]);

                $add_friend = $spdo->prepare('UPDATE users SET friend_list=CONCAT(friend_list, "?,") WHERE username = ?');
                $add_friend->execute([",", $userLoggedIn]);

                $delete_query = $spdo->prepare('DELETE FROM friend_requests WHERE user_to = ? AND user_from = ?');
                $delete_query->execute([$userLoggedIn, $user_from]);

                echo "You are now friends!";
                header("Location: requests.php");
            }

            if(isset($_POST['ignore_request' . $user_from ])) {
                $delete_query = $spdo->prepare('DELETE FROM friend_requests WHERE user_to = ? AND user_from = ?');
                $delete_query->execute([$userLoggedIn, $user_from]);
                echo "Request ignored.";
                header("Location: requests.php");

            }

            echo "
            <form class='' action='requests.php' method='POST'>
              <input type='submit' name='accept_request$user_from' class='btn btn-success' id='accept_button' value='Accept'>
              <input type='submit' name='ignore_request$user_from' class='btn btn-secondary' id='ignore_button' value='Ignore'>
            </form>";

            ?>
            <!-- <form class="" action="" method="POST">
              <input type="submit" name="accept_request<?php echo $user_from; ?>" class="btn btn-success" id="accept_button" value="Accept">
              <input type="submit" name="ignore_request<?php echo $user_from; ?>" class="btn btn-secondary" id="ignore_button" value="Ignore">
          </form> -->
            <?php

        }
    }
}

$listRequests = array('listRequests'=> listRequests($spdo, $userLoggedIn));

echo $twig->render('requests.twig', $listRequests);
?>
