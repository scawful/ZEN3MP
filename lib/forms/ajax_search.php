<?php
include "../config.php";
include ("classes/User.php");
include ("classes/Post.php");
include ("classes/Message.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if(strpos($query, "_") !== false) {
    $usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");

}
else if(count($names) == 2) {
    $usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE (username LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
}
else {
    $usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE (username LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
}

if($query != "") {
    while($row = mysqli_fetch_assoc($usersReturned)) {

      $user = new User($userLoggedIn, $spdo);

      if($row['username'] != $userLoggedIn) {
        $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
      }
      else {
        $mutual_friends = "<br />";
      }

      echo "<div class='resultDisplayBar'>
              <a href='" . $row ['username'] . "' style='color: $1485BD;'>
                <div class='liveSearchAvatar'>
                  <img src='" . $row['avatar'] . "' class='avatar'>
                </div>
                <div class='liveSearchText'>
                  <a href=/" . $row['username'] . ">" . $row['displayname'] . "</a>
                  <p id='grey'>" . $mutual_friends . "</p>
                </div>
              </a>

            </div>";
  }

}

 ?>
