<?php
require('lib/config.php');
include("lib/forms/classes/User.php");
include("lib/forms/classes/Post.php");
include("lib/forms/classes/Character.php");
include("lib/forms/classes/Message.php");
include("lib/forms/classes/Notification.php");
include("lib/forms/classes/Inventory.php");
include("lib/forms/classes/Quest.php");

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

include("lib/globals.php");
include("lib/forms/timeline_post_form.php");

switch (True) {
    case isset($_GET['news']):
        echo $twig->render('news.twig');
        break;
    case isset($_GET['board']):
        echo $twig->render('board.twig');
        break;
    case isset($_GET['users']):
        echo $twig->render('users.twig');
        break;
    case isset($_GET['store']):
        echo $twig->render('store.twig');
        break;
    case isset($_GET['world']):
        echo $twig->render('world.twig');
        break;
    case isset($_GET['privacy']):
        echo $twig->render('privacy.twig');
        break;
    case isset($_GET['rules']):
        echo $twig->render('rules.twig');
        break;
    default:
        if(isset($_SESSION['username'])) {
            echo $twig->render('home.twig');
        } else {
            echo $twig->render('login.twig');
        }
        break;
}



?>
