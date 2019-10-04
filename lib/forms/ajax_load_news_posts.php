<?php
include ("../config.php");
include ("classes/User.php");
include ("classes/Post.php");

$limit = 10; // Number of posts to be loaded per call

$posts = new Post($connect_social, $_REQUEST['userLoggedIn'], $spdo);
$posts->loadNewsPosts($_REQUEST, $limit);
?>
