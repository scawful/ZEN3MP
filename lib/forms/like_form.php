<?php
require "../config.php";
include ("classes/User.php");
include ("classes/Post.php");
include ("classes/Notification.php");

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
    <link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/src/font/typicons.min.css" />
    <link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/zen3mp.css" />
    <?php
    if(isset($_SESSION['username'])) {
        echo '<link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/' . $style . '.css" />';
      } else {
        echo '<link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/purpleStyle.css" />';
      }
    ?>
    <style>
    body {
      background: none;
    }
    </style>
    <link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/bootstrap.css" />
  </head>
  <body>
<div class="float-right">
<?php


    //Get id of post
  	if(isset($_GET['post_id'])) {
  		$post_id = $_GET['post_id'];
  	}

  	$get_likes = mysqli_query($connect_social, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
  	$row = mysqli_fetch_array($get_likes);
  	$total_likes = $row['likes'];
  	$user_liked = $row['added_by'];

    $user_details_query = mysqli_query($connect_social, "SELECT * FROM users WHERE username='$user_liked'");
    $row = mysqli_fetch_array($user_details_query);
    $total_user_likes = $row['num_likes'];

    //Like button
  	if(isset($_POST['like_button'])) {
  		$total_likes++;
  		$query = mysqli_query($connect_social, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
  		$total_user_likes++;
  		$user_likes = mysqli_query($connect_social, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
  		$insert_user = mysqli_query($connect_social, "INSERT INTO likes VALUES(0, '$userLoggedIn', '$post_id')");

  		//Insert Notification
      if($user_liked != $userLoggedIn) {
        $notification = new Notification($connect_social, $userLoggedIn, $spdo);
        $notification->insertNotification($post_id, $user_liked, "like");
      }

  	}
  	//Unlike button
  	if(isset($_POST['unlike_button'])) {
  		$total_likes--;
  		$query = mysqli_query($connect_social, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
  		$total_user_likes--;
  		$user_likes = mysqli_query($connect_social, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
  		$insert_user = mysqli_query($connect_social, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
  	}

    //Check for previous likes
  	$check_query = mysqli_query($connect_social, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
  	$num_rows = mysqli_num_rows($check_query);

    if($num_rows > 0) {
  		echo '<form action="like_form.php?post_id=' . $post_id . '" method="POST" name="unlike_form" class="comment_like">
          <button type="submit" name="unlike_button" value="Unlike" class="btn btn-primary card-link">
          <i class="typcn typcn-star-full-outline icon"></i> Unlike '. $total_likes .'</button>
  			</form>
  		';
  	}
  	else {
  		echo '<form action="like_form.php?post_id=' . $post_id . '" method="POST" name="like_form" class="comment_like">
        <button type="submit" name="like_button" value="Like" class="btn btn-primary card-link">
        <i class="typcn typcn-star-outline icon"></i> Like '. $total_likes .'</button>
  			</form>
  		';
  	}

    ?>
</div>
  </body>
</html>
<!--           <a href="#" class="btn btn-primary"><i class="typcn typcn-arrow-repeat icon"> Repost</i></a>
-->
