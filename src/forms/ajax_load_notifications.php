<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

require_once("../config.php");
require_once("../incl.php");
require_once("../auth.php");

$limit = 10; // Number of messages to be loaded per call

$character = new Character($userLoggedIn, $spdo, $rpdo);
$notification = new Notification($_REQUEST['userLoggedIn'], $character, $spdo, $rpdo);
echo $notification->getNotifications($_REQUEST, $limit);
?>
