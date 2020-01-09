<?php
namespace zen3mp;
include ("../config.php");
include ("../classes/User.php");
include ("../classes/Post.php");
include ("../auth.php");

$limit = 10; // Number of posts to be loaded per call $_REQUEST['userLoggedIn']
$user = "Secret";

$posts = new Post($user, $spdo, $rpdo);
$posts->loadNewsPosts($_REQUEST, $limit);
?>
