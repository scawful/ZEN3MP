<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../../src/config.php');
include("../../src/classes/Utils.php");
include("../../src/classes/User.php");
include("../../src/classes/Post.php");
include("../../src/classes/Message.php");
include("../../src/classes/Notification.php");
include("../../src/classes/Character.php");
include("../../src/classes/Inventory.php");
include("../../src/classes/Quest.php");
include("../../src/classes/ImageUpload.php");

require '../../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);

include("../../src/auth.php");
include("../../src/globals.php");
include("../func.php");

echo $twig->render('above/image.twig');

?>