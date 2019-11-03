<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../config.php";
include ("../classes/User.php");
include ("../classes/Post.php");
include ("../classes/Notification.php");

if (isset($_SESSION['username']))
	$userLoggedIn = $_SESSION['username'];
else
	header("Location: index.php");

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
  	if(isset($_GET['post_id'])) {
  		$post_id = $_GET['post_id'];
  	}

  	//$get_likes = mysqli_query($connect_social, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
	$get_likes = $spdo->prepare('SELECT likes, added_by FROM posts WHERE id = ?');
	$get_likes->execute([$post_id]);
	$row = $get_likes->fetch();
  	$total_likes = $row['likes'];
  	$user_liked = $row['added_by'];

	$user_details_query = $spdo->prepare('SELECT * FROM users WHERE username = ?');
	$user_details_query->execute([$user_liked]);
	$row = $user_details_query->fetch();
    $total_user_likes = $row['num_likes'];

    //Like button
  	if(isset($_POST['like_button'])) {
  		$total_likes++;
		$query = $spdo->prepare('UPDATE posts SET likes = ? WHERE id = ?');
		$query->execute([$total_likes, $post_id]);
		$total_user_likes++;
		$user_likes = $spdo->prepare('UPDATE users SET num_likes = ? WHERE username = ?');
		$user_likes->execute([$total_user_likes, $user_liked]);
		$insert_user = $spdo->prepare('INSERT INTO likes VALUES (0, ?, ?)');
		$insert_user->execute([$userLoggedIn, $post_id]);

  		//Insert Notification
      if($user_liked != $userLoggedIn) {
        $notification = new Notification($userLoggedIn, $spdo);
        $notification->insertNotification($post_id, $user_liked, "like");
      }

  	}
  	//Unlike button
  	if(isset($_POST['unlike_button'])) {
  		$total_likes--;
		$query = $spdo->prepare('UPDATE posts SET likes = ? WHERE id = ?');
		$query->execute([$total_likes, $post_id]);
  		$total_user_likes--;
		$user_likes = $spdo->prepare('UPDATE users SET num_likes = ? WHERE username = ?');
		$user_likes->execute([$total_user_likes, $user_liked]);
		$insert_user = $spdo->prepare('DELETE FROM likes WHERE username = ? AND post_id = ?');
		$insert_user->execute([$userLoggedIn, $post_id]);
  	}

    //Check for previous likes
	$check_query = $spdo->prepare('SELECT * FROM likes WHERE username = ? AND post_id = ?');
	$check_query->execute([$userLoggedIn, $post_id]);
	$num_rows = $check_query->rowCount();

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
