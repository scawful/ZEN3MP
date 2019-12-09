<?php
namespace zen3mp;
use \PDO;

include "../config.php";
include ("../classes/User.php");
include ("../classes/Post.php");
include ("../classes/Message.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if(strpos($query, "_") !== false) {
    $usersReturned = $spdo->query("SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
    //$usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
  }
  else if(count($names) == 2) {
    $usersReturned = $spdo->query("SELECT * FROM users WHERE (username LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
    //$usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE (username LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
  }
  else {
    $usersReturned = $spdo->query("SELECT * FROM users WHERE (username LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
    //$usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE (username LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
  }

if($query != "") {
    while($row = $usersReturned->fetch(PDO::FETCH_ASSOC)) {

        $user = new User($userLoggedIn, $spdo);

        if($row['username'] != $userLoggedIn) {
            $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common.";
        }
        else {
            $mutual_friends = "";
        }

        if($user->isFriend($row['username'])) {
            echo "<div class='resultDisplay'>
                      <a href='?inbox&u=" . $row['username'] . "'
                      <div class='liveSearchProfilePic'>
                          <img src='" . $row['avatar'] . "' class='avatar'>

                      <div class='liveSearchText'>
                          <p>" . $row['displayname'] . "</p>
                          <p id='grey'>" . $mutual_friends . "</p>

                      </div>
                      </div>
                      </a>
                      </div>
            ";


        }

    }
}
?>
