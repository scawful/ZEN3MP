<?php
include ("../config.php");
include ("../classes/User.php");
include ("../classes/Post.php");

$limit = 10; // Number of posts to be loaded per call $_REQUEST['userLoggedIn']
$user = "Secret";

$posts = new Post($connect_social, $user, $spdo);
$posts->loadNewsPosts($_REQUEST, $limit);
?>
