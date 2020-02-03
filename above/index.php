<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../src/config.php');
include("../src/incl.php");

require '../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);

include("../src/auth.php");
include("../src/globals.php");
include("func.php");

switch (True) {
    case isset($_GET['twitterpost']):
        echo $twig->render('above/forms/twitter_post.twig');
        break;
    case isset($_GET['items']):
        echo $twig->render('above/items.twig');
        break;
    case isset($_GET['newpost']):
        echo $twig->render('above/newpost.twig');
        break;
    case isset($_GET['newcategory']):
        echo $twig->render('above/new_category.twig');
        break;
    case isset($_GET['newpage']):
        echo $twig->render('above/new_page.twig');
        break;
    case isset($_GET['viewpages']):
        echo $twig->render('above/view_pages.twig');
        break;
    case isset($_GET['editpage']):
        echo $twig->render('above/edit_page.twig');
        break;
    case isset($_GET['viewcategories']):
        echo $twig->render('above/view_category.twig');
        break;
    case isset($_GET['newitem']):
        echo $twig->render('above/new_item.twig');
        break;
    case isset($_GET['delete']):
        echo $twig->render('above/delete.twig');
        break;
    case isset($_GET['users']):
        echo $twig->render('above/users.twig');
        break;
    default:
        echo $twig->render('above/above.twig');
        break;
}

?>
