<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

require('src/config.php');
include("src/classes/Utils.php");
include("src/classes/User.php");
include("src/classes/Post.php");
include("src/classes/Character.php");
include("src/classes/Message.php");
include("src/classes/Notification.php");
include("src/classes/Inventory.php");
include("src/classes/Quest.php");

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

include("src/auth.php");
include("src/globals.php");

$post = new Post($connect_social, $userLoggedIn, $spdo);

$twig->addGlobal('post', $post);
$twig->addGlobal('post_id', $post_id);

echo $twig->render('post.twig');
?>
