<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

require_once('src/config.php');
require_once('src/incl.php');
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
        echo $twig->render('info/news.twig');
        break;
    case isset($_GET['board']):
        echo $twig->render('social/board.twig');
        break;
    case isset($_GET['users']):
        echo $twig->render('social/users.twig');
        break;
    case isset($_GET['store']):
        if($isLoggedIn == true) {
            echo $twig->render('rpg/store.twig');
        } else {
            echo $twig->render('login.twig');
        }
        break;
    case isset($_GET['world']):
        if($isLoggedIn == true)
            echo $twig->render('rpg/world.twig');
        else
            echo $twig->render('login.twig');
        break;
    case isset($_GET['inbox']):
        if($isLoggedIn == true)
            echo $twig->render('social/messages.twig');
        else
            echo $twig->render('login.twig');
        break;
    case isset($_GET['privacy']):
        echo $twig->render('info/privacy.twig');
        break;
    case isset($_GET['rules']):
        echo $twig->render('info/rules.twig');
        break;
    case isset($_GET['requests']):
        if($isLoggedIn == true)
            echo $twig->render('social/requests.twig');
        else
            echo $twig->render('login.twig');
        break;
    case isset($_GET['verify']):
        echo $twig->render('verify.twig');
        break;
    case isset($_GET['zelda']):
        echo $twig->render('info/zelda.twig');
        break;
    case isset($_GET['faq']):
        echo $twig->render('info/faq.twig');
        break;
    case isset($_GET['logout']):
        include ('lib/pages/logout.php');
        break;
    default:
        if($isLoggedIn == true) {
            echo $twig->render('social/home.twig');
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
