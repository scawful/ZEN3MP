<?php
namespace zen3mp;
$requests = "";
$confirm = "";

$stmt = $spdo->prepare('SELECT * FROM friend_requests WHERE user_to = ?');
$stmt->execute([$userLoggedIn]);

if($stmt->rowCount() == 0) {
    $requests = "You have no friend requests at this time.";
} else {

    while($row = $stmt->fetch()) {
        $user_from = $row['user_from'];
        $user_from_obj = new User($user_from, $spdo);

        $requests .= $user_from_obj->getUsername() . " sent you a friend request!";
        $user_from_friend_array = $user_from_obj->getFriendList();

        if(isset($_POST['accept_request' . $user_from ])) {
            $add_friend = $spdo->prepare('UPDATE users SET friend_list=CONCAT(friend_list, ?) WHERE username = ?');
            $add_friend->execute(["$user_from,", $userLoggedIn]);

            $add_friend = $spdo->prepare('UPDATE users SET friend_list=CONCAT(friend_list, "?,") WHERE username = ?');
            $add_friend->execute([",", $userLoggedIn]);

            $delete_query = $spdo->prepare('DELETE FROM friend_requests WHERE user_to = ? AND user_from = ?');
            $delete_query->execute([$userLoggedIn, $user_from]);

            $confirm .=  "You are now friends!";
            header("Location: requests.php");
        }

        if(isset($_POST['ignore_request' . $user_from ])) {
            $delete_query = $spdo->prepare('DELETE FROM friend_requests WHERE user_to = ? AND user_from = ?');
            $delete_query->execute([$userLoggedIn, $user_from]);
            $confirm .=  "Request ignored.";
            header("Location: requests.php");

        }

        $requests .= "
        <form class='' action='requests.php' method='POST'>
          <input type='submit' name='accept_request$user_from' class='btn btn-success' id='accept_button' value='Accept'>
          <input type='submit' name='ignore_request$user_from' class='btn btn-secondary' id='ignore_button' value='Ignore'>
        </form>";

    }
}
?>