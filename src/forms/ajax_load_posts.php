<?php
namespace zen3mp;

include ("../config.php");
include ("../classes/User.php");
include ("../classes/Post.php");

$limit = 10; // Number of posts to be loaded per call

$posts = new Post($_REQUEST['userLoggedIn'], $spdo, $rpdo);
$posts->loadPostsFriends($_REQUEST, $limit);
?>
