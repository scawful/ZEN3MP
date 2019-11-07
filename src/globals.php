<?php
$user_obj = new User($userLoggedIn, $spdo);
$character = new Character($userLoggedIn, $spdo, $rpdo);
$inventory = new Inventory($connect_social, $connect_rpg, $userLoggedIn, $spdo);
$quest_obj = new Quest($userLoggedIn, $rpdo, $spdo);
$messages = new Message($connect_social, $userLoggedIn, $spdo);
$notifications = new Notification($userLoggedIn, $spdo);

if(isset($_GET['q']))
    $quest_id = $_GET['q'];
else
    $quest_id = 0;

if(isset($_GET['p']))
    $page_id = $_GET['p'];
else
    $page_id = 0;

if (!empty($_GET['item']) && (intval($_GET['item']) == $_GET['item']))
    $item_id = $_GET['item'];
else
    $item_id = 0;

if(isset($_GET['u']))
    $user_to = $_GET['u'];
else {
    $user_to = $messages->getMostRecentUser();
    if($user_to == false)
        $user_to = 'new';
}

$style = $user_obj->getUserStyle();
$session_id = session_id();

$twig->addGlobal('login_flag', $login_flag);
$twig->addGlobal('userLoggedIn', $userLoggedIn);
$twig->addGlobal('style', $style);
$twig->addGlobal('user', $user_obj);
$twig->addGlobal('character', $character);
$twig->addGlobal('notifications', $notifications);
$twig->addGlobal('messages', $messages);
$twig->addGlobal('quest', $quest_obj);
$twig->addGlobal('q_id', $quest_id);
$twig->addGlobal('p_id', $page_id);
$twig->addGlobal('i_id', $item_id);
$twig->addGlobal('u_to', $user_to);
$twig->addGlobal('inventory', $inventory);
$twig->addGlobal('session', $_SESSION);
$twig->addGlobal('Z3MP_POST', $_POST);
$twig->addGlobal('session_id', $session_id);
$twig->addGlobal('error_array', $error_array);
?>
