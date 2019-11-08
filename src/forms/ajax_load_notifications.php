<?php
namespace zen3mp;
use zen3mp\Utils as Utils;

include ("../config.php");
include ("../classes/Utils.php");
include ("../classes/User.php");
include ("../classes/Notification.php");

$limit = 7; // Number of messages to be loaded per call

$notification = new Notification($_REQUEST['userLoggedIn'], $spdo);
echo $notification->getNotifications($_REQUEST, $limit);
?>
