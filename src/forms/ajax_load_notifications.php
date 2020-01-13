<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

require_once("../config.php");
require_once("../incl.php");

$limit = 7; // Number of messages to be loaded per call

$notification = new Notification($_REQUEST['userLoggedIn'], $spdo);
echo $notification->getNotifications($_REQUEST, $limit);
?>
