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
$utils = new Utils();

include("src/auth.php");
include("src/globals.php");
include("src/forms/register_form.php");
include("src/forms/login_form.php");
include("src/forms/timeline_post_form.php");
include("src/forms/message_post_form.php");

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
        if($isLoggedIn == true) {
            echo $twig->render('store.twig');
        } else {
            echo $twig->render('login.twig');
        }
        break;
    case isset($_GET['world']):
        if($isLoggedIn == true)
            echo $twig->render('world.twig');
        else
            echo $twig->render('login.twig');
        break;
    case isset($_GET['inbox']):
        if($isLoggedIn == true)
            echo $twig->render('messages.twig');
        else
            echo $twig->render('login.twig');
        break;
    case isset($_GET['privacy']):
        echo $twig->render('privacy.twig');
        break;
    case isset($_GET['rules']):
        echo $twig->render('rules.twig');
        break;
    case isset($_GET['requests']):
        if($isLoggedIn == true)
            echo $twig->render('requests.twig');
        else
            echo $twig->render('login.twig');
        break;
    case isset($_GET['verify']):
        echo $twig->render('verify.twig');
        break;
    case isset($_GET['zelda']):
        echo $twig->render('zelda.twig');
        break;
    case isset($_GET['faq']):
        echo $twig->render('faq.twig');
        break;
    case isset($_GET['logout']):
        include ('lib/pages/logout.php');
        break;
    default:
        if($isLoggedIn == true) {
            echo $twig->render('home.twig');
        } else {
            echo $twig->render('login.twig');
        }
        break;
}

if(isset($_POST['register_button'])) {
	echo '
	<script>

	$(document).ready(function() {
		$("#first").hide();
		$("#second").show();
	});
	</script>
	';
}

?>
