<?php
include ('lib/header.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<div id="mainBox" class="boxes">
  <div class="titleBar">Friend Requests</div>
  <div class="article">
  <?php

  $query = mysqli_query($connect_social, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
  if(mysqli_num_rows($query) == 0) {
    echo "You have no friend requests at this time.";
  }
  else {

      while($row = mysqli_fetch_array($query)) {
            $user_from = $row['user_from'];
            $user_from_obj = new User($connect_social, $user_from);

            echo $user_from_obj->getUsername() . " sent you a friend request!";

            $user_from_friend_array = $user_from_obj->getFriendList();

            if(isset($_POST['accept_request' . $user_from ])) {
                $add_friend_query = mysqli_query($connect_social, "UPDATE users SET friend_list=CONCAT(friend_list, '$user_from,') WHERE username='$userLoggedIn'");
                $add_friend_query = mysqli_query($connect_social, "UPDATE users SET friend_list=CONCAT(friend_list, '$userLoggedIn,') WHERE username='$user_from'");
                $delete_query = mysqli_query($connect_social, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                echo "You are now friends!";
                header("Location: requests.php");
            }

            if(isset($_POST['ignore_request' . $user_from ])) {
              $delete_query = mysqli_query($connect_social, "SELECT FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
              echo "Request ignored.";
              header("Location: requests.php");

            }

            ?>
            <form class="" action="requests.php" method="POST">
              <input type="submit" name="accept_request<?php echo $user_from; ?>" class="btn btn-success" id="accept_button" value="Accept">
              <input type="submit" name="ignore_request<?php echo $user_from; ?>" class="btn btn-secondary" id="ignore_button" value="Ignore">
            </form>
            <?php

        }
    }

   ?>
 </div>
</div>

<div id="profileBox" class="boxes">
  <div class="titleBar">
    header
  </div>

</div>

<?php
include ('lib/footer.php');
?>
