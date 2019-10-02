<?php
require('lib/config.php');
include("lib/forms/classes/User.php");
include("lib/forms/classes/Post.php");
include("lib/forms/classes/Message.php");
include("lib/forms/classes/Notification.php");
include("lib/forms/classes/Character.php");
include("lib/forms/classes/Inventory.php");
include("lib/forms/classes/Quest.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_SESSION['username']))
{
    $userLoggedIn = $_SESSION['username'];
    $usersDetailsQuery = mysqli_query($connect_social, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($usersDetailsQuery);
    $num_friends = (substr_count($user['friend_list'], ",")) - 1;

} else {
    ## header("Location: index.php");
    session_destroy();
    $userLoggedIn = "Secret";
}
// Main Timeline Post
if(isset($_POST['post']))
{

  $uploadOk = 1;
	$imageName = $_FILES['fileToUpload']['name'];
	$errorMessage = "";

	if($imageName != "")
  {
		$targetDir = "img/uploads/posts/";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if($_FILES['fileToUpload']['size'] > 10000000)
    {
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}

		if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
			$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
			$uploadOk = 0;
		}

		if($uploadOk)
    {
			if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
				//image uploaded okay
			}
			else {
				//image did not upload
				$uploadOk = 0;
			}
		}

	}

  if($uploadOk) {
    $post = new Post($connect_social, $userLoggedIn, $spdo);
    $newPostBody = (!isset($_POST['post_text']) || empty($_POST['post_text'])) ? "" : $_POST['post_text'];
    if($newPostBody != "") {
      $post->submitPost($newPostBody, 'none', $imageName);
    }
    else {
      $post->submitPhoto('none', $imageName);
    }
  }
  else {
    echo "<div style='text-align: center; padding: 2px;' class='alert alert-danger'>
            $errorMessage
          </div>";
  }


}
$character = new Character($userLoggedIn, $spdo, $rpdo);
$inventory = new Inventory($connect_social, $connect_rpg, $userLoggedIn, $spdo);
$quest_obj = new Quest($userLoggedIn, $rpdo, $spdo);
//unread messages
$messages = new Message($connect_social, $userLoggedIn, $spdo);
$num_messages = $messages->getUnreadNumber();

//unread notifications
$notifications = new Notification($connect_social, $userLoggedIn, $spdo);
$num_notifications = $notifications->getUnreadNumber();

//unanswered friend friend_requests
$user_obj = new User($userLoggedIn, $spdo);
$num_requests = $user_obj->getNumberOfFriendRequests();

$style = $user_obj->getUserStyle();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  	<title>Zeniea</title>

    <meta charset="UTF-8">
    <meta name="author" content="Justin Scofield">
    <meta name="keywords" content="Zeniea,ZEN3MP,HTML,CSS,XML,JavaScript,PHP,MySQL,jQuery,Bootstrap,RPG,Quest,Fight,Trade,Talk,Social,Network">
    <meta name="description" content="A modern role-playing quest game built into a social network! Uses HTML5, PHP, and Javascript to create a dynamic user experience. Currently in development and accepting registrants.">
    <meta name="viewport" content="width=device-width, initial-scale=0.7">

    <!-- Zeniea Stylesheets -->
    <?php
    if(isset($_SESSION['username'])) {
        echo '<link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/' . $style . '.css" />';
      } else {
        echo '<link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/purpleStyle.css" />';
      }
    ?>

    <link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/zen3mp.css" />

    <!-- Zootstrap -->
    <link rel="stylesheet" type="text/css" href="https://zeniea.com/lib/css/bootstrap.css" />

    <!-- Icon Sets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
		<link rel="stylesheet" href="https://zeniea.com/lib/css/src/font/typicons.css" />

    <!-- Google Garbage -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- Bootstrap/box Javascript -->
    <script src="https://zeniea.com/lib/js/bootstrap.js"></script>
    <script src="https://zeniea.com/lib/js/bootbox.js"></script>

    <!-- Zeniea Javascript -->
    <script src="https://zeniea.com/lib/js/zen3mp.js"></script>
    <script src="https://zeniea.com/lib/js/register.js"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-27378985-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-27378985-1');
    </script>

</head>
<body>

  <div id="navBox" class="boxes" style=" "><span class="invis"></span>

      <a href="/" title="Home"><i class="typcn typcn-home icon btnPurp"></i></a>
      <?php
      if(isset($_SESSION['username'])) {
      echo '<a href="/?world" title ="World"><i class="typcn typcn-world icon btnPurp"></i></a>
      <a href="/?store" title ="Item Store"><i class="typcn typcn-shopping-bag icon btnPurp"></i></a>';
      }
      ?>
      <a href="/?users" title ="Users"><i class="typcn typcn-group icon btnPurp"></i></a>
      <a href="/?news" title ="News"><i class="typcn typcn-news icon btnPurp"></i></a>

      <div class="search-container search">
          <form action="search.php" method="GET" class="search-box-align" name="search_form">
            <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." style="color: #E3DFFF;" autocomplete="off" id="search_text_input">
              <div class="search-btn search-btn-default button_holder">
                <i class="fa fa-search icon btnPurp"></i>
              </div>
          </form>
        <div class="search_results"></div>
        <div class="search_results_footer_empty">
        </div>
      </div>

      <div class="profile-options">
        <?php
        if(isset($_SESSION['username'])) {
            $user_obj->aboveButton();
            echo '<a href="/' . $userLoggedIn . '" title="Profile"><i class="typcn typcn-user icon btnPurp"></i></a>';
                  if($num_requests > 0 ) {
                  echo '<a href="requests.php"><span class="badge badge-notification" id="unread_requests">
                    ' . $num_requests . '
                    </span></a>
                    ';
                  }
            echo '<a href="/?inbox" title="Inbox"><i class="typcn typcn-mail icon btnPurp"></i></a>
                  <a href="javascript:void(0);" onclick="getDropdownData(\'' . $userLoggedIn . '\', \'message\')" title="Messages">
                  <i class="typcn typcn-messages icon btnPurp"></i></a>';
                  if($num_messages > 0 ) {
                  echo '<span class="badge badge-notification" id="unread_messages">
                    ' . $num_messages . '
                    </span>
                    ';
                  }
            echo '<a href="javascript:void(0);" onclick="getDropdownData(\'' . $userLoggedIn . '\', \'notification\')" title="Notifications">
            <i class="typcn typcn-bell icon btnPurp"></i>';
            if($num_notifications > 0 ) {
            echo '<span class="badge badge-notification" id="unread_notifications">
              ' . $num_notifications . '
              </span>
              ';
            }
          echo '</a><a href="/?logout" title="Logout"><span class="typcn typcn-eject icon btnPurp"></span></a>';
        } else {
            echo '';
        }
        ?>
      </div>

      <div class="dropdown_data_window" style="height:0px; border:none;"></div>
      <input type="hidden" id="dropdown_data_type" value="">
    </div>

	<?php
  $container_str = "";

  if(isset($_SESSION['username']) || isset($_GET['users']) || isset($_GET['zelda'])  || isset($_GET['rules'])) {
    $container_str = '<div id="container">';
    if(isset($_GET['news']) || isset($_GET['world']) || isset($_GET['verify']) || isset($_GET['users'])) {
      $container_str = '<div id="container4">';
    }
    echo $container_str;
    } else {
      $container_str = '<div id="container3">';
      echo $container_str;
  }
  ?>

    <header>
    Zeniea.com
    <div class="float-right" style="font-size: 8pt;">
      <script>
        /*var wittySubtitle = [
          "Stop right there, criminal scum!",
          "Get in the robot, shinji.",
          "zQuest is best quest (RIP 2013-2017)",
          "No one man should have all that power.",
          "In other news, scientists theorize that CHEESECAKE.",
          "Your drill is the drill to pierce the heavens."
        ];
        var randomItem = wittySubtitle[Math.floor(Math.random()*wittySubtitle.length)];
        document.write(randomItem);
        var localTime = new Date();
        document.write("<div style='float: right;'>" + localTime + "<br></div>");
      </script>
    </div>
    </header>

    <script>
      $('#customFile').on('change',function(){
          //get the file name
          var fileName = $(this).val();
          //replace the "Choose a file" label
          $(this).next('.custom-file-label').html(fileName);
      })
      function resizeIframe(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
      }

      var userLoggedIn = '<?php echo $userLoggedIn; ?>';

      $(document).ready(function() {

        $('.dropdown_data_window').scroll(function() {
          var inner_height = $('.dropdown_data_window').innerHeight(); //Div containing posts
          var scroll_top = $('.dropdown_data_window').scrollTop();
          var page = $('.dropdown_data_window').find('.nextPageDropDownData').val();
          var noMoreData = $('.dropdown_data_window').find('.noMoreDropDownData').val();

          if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {

            var pageName; //holds name of page to send ajax request to
            var type = $('#dropdown_data_type').val();

            if(type ==  'notification')
                pageName = "ajax_load_notifications.php";
            else if (type == 'message')
                pageName = "ajax_load_messages.php";

            var ajaxReq = $.ajax({
              url: "lib/forms/" + pageName,
              type: "POST",
              data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
              cache:false,
              async:false,

              success: function(response) {
                $('.dropdown_data_window').find('.nextPageDropDownData').remove(); //Removes current .nextpage
                $('.dropdown_data_window').find('.noMoreDropDownData').remove(); //Removes current .nextpage

                $('.dropdown_data_window').append(response);
              }
            });

          } //End if

          return false;

        }); //End (window).scroll(function())


      });

      </script>
