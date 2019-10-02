<?php include ('lib/header.php');
define('DIV', '<div class = "titleBar">');
define('NDIV', '</div>');
switch (True) {
    case isset($_GET['world']):
        include ('lib/pages/world.php');
        break;
    case isset($_GET['news']):
        include ('lib/pages/news.php');
        break;
    case isset($_GET['verify']):
        include ('lib/pages/blank_box.php');
        include ('lib/pages/verify.php');
        break;
    case isset($_GET['store']):
        include ('lib/pages/store.php');
        break;
    case isset($_GET['zelda']):
        include ('lib/pages/blank_box.php');
        include ('lib/pages/zelda.html');
        break;
    case isset($_GET['users']):
        include ('lib/pages/users.php');
        break;
    case isset($_GET['inbox']):
        include ('lib/pages/profile_box.php');
        include ('lib/pages/messages.php');
        break;
    case isset($_GET['settings']):
        include ('lib/pages/profile_box.php');
        include ('lib/pages/settings.php');
        break;
    case isset($_GET['rules']):
        include ('lib/pages/blank_box.php');
        include ('lib/pages/rules.html');
        break;
    case isset($_GET['faq']):
        include ('lib/pages/blank_box.php');
        include ('lib/pages/faq.html');
        break;
    case isset($_GET['logout']):
        include ('lib/pages/logout.php');
        break;
    case isset($_GET['privacy']):
        include ('lib/pages/blank_box.php');
        include ('lib/pages/privacy.html');
        break;
    default:
				if(isset($_SESSION['username'])) {
            include ('lib/pages/profile_box.php');
		        include ('lib/pages/timeline.php');
				} else {
						include ('lib/pages/register.php');
				}
        break;
}
include('lib/footer.php');
?>
</body>
</html>
