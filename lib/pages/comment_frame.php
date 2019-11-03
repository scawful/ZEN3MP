<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../../src/config.php";
include ("../../src/classes/User.php");
include ("../../src/classes/Post.php");
include ("../../src/classes/Notification.php");

if (isset($_SESSION['username'])) {
  $userLoggedIn = $_SESSION['username'];
  $user_details_query = mysqli_query($connect_social, "SELECT * FROM users WHERE username='$userLoggedIn'");
  $user = mysqli_fetch_array($user_details_query);
}
else {
  	header("Location: index.php");
}
$user_obj = new User($userLoggedIn, $spdo);
$style = $user_obj->getUserStyle();
?>
<html>
  <head>
    <?php
    if(isset($_SESSION['username'])) {
        echo '<link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/' . $style . '.css" />';
      } else {
        echo '<link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/purpleStyle.css" />';
      }
    ?>
    <link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/zen3mp.css" />

    <style>
    body {
      background: none; !important
    }
    </style>

  </head>
  <body>

    <script>
    function toggle() {
      var element = document.getElementById("comment_section");

    if(element.style.display == "block")
          element.style.display = "none";
      else
      element.style.display = "block";
    }
  	</script>
     <?php
   	//Get id of post
   	if(isset($_GET['post_id'])) {
   		$post_id = $_GET['post_id'];
   	}

   	$user_query = mysqli_query($connect_social, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
   	$row = mysqli_fetch_array($user_query);

   	$posted_to = $row['added_by'];
    $user_to = $row['user_to'];

   	if(isset($_POST['postComment' . $post_id])) {
   		$post_body = $_POST['post_body'];
   		$post_body = mysqli_escape_string($connect_social, $post_body);
   		$date_time_now = date("Y-m-d H:i:s");
   		$insert_post = mysqli_query($connect_social, "INSERT INTO comments VALUES (0, '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");

      if($posted_to != $userLoggedIn) {
        $notification = new Notification($userLoggedIn, $spdo);
        $notification->insertNotification($post_id, $posted_to, "comment");
      }

      if($user_to != 'none' && $user_to != $userLoggedIn) {
        $notification = new Notification($userLoggedIn, $spdo);
        $notification->insertNotification($post_id, $user_to, "profile_comment");
      }

      $get_commenters = mysqli_query($connect_social, "SELECT * FROM comments WHERE post_id='$post_id'");
      $notified_users = array();
      while($row = mysqli_fetch_array($get_commenters)) {

        if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to
            && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)) {

              $notification = new Notification($userLoggedIn, $spdo);
              $notification->insertNotification($returned_id, $row['posted_by'], "comment_non_owner");

              array_push($notified_users, $row['posted_by']);
        }

      }

      echo "<p>Comment Posted! </p>";
   	}
   	?>

    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
  		<!-- <textarea name="post_body" style="margin: 5px; width: 75%;"></textarea> -->
      <input type="text" class="form-control" name="post_body" id="post_text" placeholder="Type something here...">
  		<!-- <input type="submit" name="" value="Post"> -->
      <button type="submit" name="postComment<?php echo $post_id; ?>" id="post_button" value="POST" class="btn btn-post">POST</button>
  	</form>
    <!-- Load comments -->
    <div class="comment_section">
      <div class="card">
    <?php
    $get_comments = mysqli_query($connect_social, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
    $count = mysqli_num_rows($get_comments);

    if($count != 0) {

        while($comment = mysqli_fetch_array($get_comments)) {

            $comment_body = $comment['post_body'];
            $posted_to = $comment['posted_to'];
            $posted_by = $comment['posted_by'];
            $date_added = $comment['date_added'];
            $removed = $comment['removed'];

            //Timeframe
            $date_time_now = date("Y-m-d H:i:s");
            $start_date = new DateTime($date_added); // Time of post
            $end_date = new DateTime($date_time_now); // current time
            $interval = $start_date->diff($end_date);
            if($interval->y >= 1) {
              if($interval == 1)
                $time_message = $interval->y . " year ago"; // 1 year ago
              else
                $time_message = $interval->y . " years ago"; // 1+ year ago
            }
            else if ($interval-> m >= 1) {
              if($interval->d == 0) {
                $days = " ago";
              }
              else if($interval->d == 1) {
                $days = $interval->d . " day ago";
              }
              else {
                $days = $interval->d . " days ago";
              }

              if($interval->m == 1){
                $time_message = $interval->m . " month " . $days;
              } else {
                $time_message = $interval->m . " months " . $days;
              }
            }
            else if($interval->d >= 1) {
              if($interval->d == 1) {
                $time_message = "Yesterday";
              }
              else {
                $time_message = $interval->d . " days ago";
              }
            }
            else if($interval->h >= 1) {
              if($interval->h == 1) {
                $time_message = $interval->h . " hour ago";
              }
              else {
                $time_message = $interval->h . " hours ago";
              }
            }
            else if($interval->i >= 1) {
              if($interval->i == 1) {
                $time_message = $interval->i . " minute ago";
              }
              else {
                $time_message = $interval->i . " minutes ago";
              }
            }
            else {
              if($interval->s < 30) {
                $time_message = "Just now";
              }
              else {
                $time_message = $interval->s . " seconds ago";
              }
            }

            $user_obj = new User($posted_by, $spdo);
            ?>
              <ul id="comments">
                <li class="cmmnt">
                <div class="avatar"><a href="<?php echo $posted_by; ?>" target="_parent">
                  <img src="<?php echo $user_obj->getAvatar(); ?>" title="<?php echo $posted_by; ?>" class="avatar">
                </a></div>
                <a href="/<?php echo $posted_by; ?>" target="_parent">
                  <?php echo $user_obj->getDisplayName(); ?>
                </a>
                <span class="postTime"><?php echo $time_message ?></span>
                <div class="cmmnt-content">
                  <?php echo $comment_body; ?>
                  <hr>
                </div>
              </li>
              </ul>

            <?php

      }
    }
    else {
      echo "<center><br>No comments to show.<br><br></center>";
    }

    ?>
</div>
  </div>

  </body>
</html>
