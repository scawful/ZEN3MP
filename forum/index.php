<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../src/config.php');
include("../src/classes/Utils.php");
include("../src/classes/User.php");
include("../src/classes/Post.php");
include("../src/classes/Message.php");
include("../src/classes/Notification.php");
include("../src/classes/Character.php");
include("../src/classes/Inventory.php");
include("../src/classes/Quest.php");
include("../src/classes/Board.php");

require '../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);

include("../src/auth.php");
include("../src/globals.php");
include("../src/forms/topic_form.php");

if(isset($_GET['board'])) {
    $board_id = $_GET['board'];
} else {
    $board_id = 0;
}

if(isset($_GET['new_topic'])) {
    $category = $_GET['new_topic'];
} else {
    $category = 0;
}

if(isset($_GET['topic'])) {
    $topic_id = $_GET['topic'];
} else {
    $topic_id = 0;
}

if(isset($_GET['reply_topic'])) {
    $reply_id = $_GET['reply_topic'];
} else {
    $reply_id = 0;
}

$num_replies = $board_obj->getNumReplies($topic_id);
$num_replies--;

$twig->addGlobal('board_id', $board_id);
$twig->addGlobal('category', $category);
$twig->addGlobal('topic_id', $topic_id);
$twig->addGlobal('reply_id', $reply_id);
$twig->addGlobal('num_replies', $num_replies);

switch (True) 
{
    case isset($_GET['board']):
        echo $twig->render('social/board.twig');
        break;
    case isset($_GET['new_topic']):
        echo $twig->render('social/new_topic.twig');
        break;
    case isset($_GET['topic']):
        echo $twig->render('social/topic.twig');
        break;
    case isset($_GET['reply_topic']):
        echo $twig->render('social/reply_topic.twig');
        break;
    default:
        echo $twig->render('social/forum.twig');
        break;
}

?>
