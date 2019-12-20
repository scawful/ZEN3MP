<?php
require('src/config.php');
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

include("src/globals.php");
include("src/forms/verify_form.php");

echo $twig->render('rpg/char_sheet.twig');

?>
