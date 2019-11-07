<?php
include "../config.php";
include ("../classes/User.php");
include ("../classes/Message.php");
include ("../classes/Character.php");
include ("../classes/Inventory.php");
include ("../classes/Quest.php");
include ("../classes/Notification.php");

require ('../../vendor/autoload.php');

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);

include ("../auth.php");
include ("../globals.php");

$limit = 7; // Number of messages to be loaded per call

$message = new Message($connect_social, $userLoggedIn, $spdo);
echo $message->getConvosDropdown($_REQUEST, $limit);
?>
