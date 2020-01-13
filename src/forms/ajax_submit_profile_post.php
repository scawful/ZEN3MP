<?php
namespace zen3mp;
include "../config.php";
include ("../classes/User.php");
include ("../classes/Character.php");
include ("../classes/Post.php");
include ("../classes/Notification.php");


if(isset($_POST['post_body'])) 
{
	$image = "";
	$post = new Post($_POST['user_from'], $spdo, $rpdo);
	$post->submitPost($_POST['post_body'], $_POST['user_to'], $image);
}


?>
