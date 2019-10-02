<?php
require('../lib/config.php');
include("../lib/forms/classes/User.php");
include("../lib/forms/classes/Post.php");
include("../lib/forms/classes/Message.php");
include("../lib/forms/classes/Notification.php");
include("../lib/forms/classes/Character.php");
include("../lib/forms/classes/Inventory.php");
include("../lib/forms/classes/Quest.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);

include("../lib/globals.php");

echo $twig->render('above.twig');

?>
