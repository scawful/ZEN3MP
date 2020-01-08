<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

require('src/config.php');
require('src/incl.php');

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

include("src/auth.php");
include("src/globals.php");

$post = new Post($userLoggedIn, $spdo);

$twig->addGlobal('post', $post);
$twig->addGlobal('post_id', $post_id);

echo $twig->render('social/post.twig');
?>
