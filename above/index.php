<?php
require('../lib/config.php');
include("../lib/forms/classes/User.php");
include("../lib/forms/classes/Post.php");
include("../lib/forms/classes/Message.php");
include("../lib/forms/classes/Notification.php");
include("../lib/forms/classes/Character.php");
include("../lib/forms/classes/Inventory.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  if(isset($_SESSION['username']))
  {
      $userLoggedIn = $_SESSION['username'];
      $admin_auth_query = mysqli_query($connect_social, "SELECT user_title FROM users WHERE username='$userLoggedIn'");
      $admin_auth_code = "Admin";
      if($admin_auth_query != $admin_auth_code) {

      }
  } else {
    header("Location: https://zeniea.com/");
  }

  if(isset($_POST['news_post']))
  {
    $post = new Post($connect_social, $userLoggedIn);
    $newsPostBody = (!isset($_POST['post_text']) || empty($_POST['post_text'])) ? "" : $_POST['post_text'];
    $newsPostTitle = (!isset($_POST['post_title']) || empty($_POST['post_title'])) ? "" : $_POST['post_title'];
    $added_by = $_POST['added_by'];
    if($newsPostBody != "") {
      $post->submitNewsPost($newsPostTitle, $newsPostBody, $added_by);
    }
    else {
      echo "<div style='text-align: center; padding: 2px;' class='alert alert-danger'>
              Error: News post was empty!
            </div>";
    }
  }

  if(isset($_POST['new_item']))
  {
    $item = new Inventory($connect_social, $connect_rpg, $userLoggedIn);
    $item_name = $_POST['item-name'];
    $item_type = $_POST['item-type'];
    $item_desc = $_POST['item-desc'];
    $item_price = $_POST['item-price'];
    $req_level = $_POST['req-level'];
    $equip_zone = $_POST['equip-zone'];
    $str = $_POST['strength'];
    $int = $_POST['intelligence'];
    $wpr = $_POST['willpower'];
    $agt = $_POST['agility'];
    $spd = $_POST['speed'];
    $end = $_POST['endurance'];
    $per = $_POST['personality'];
    $wsd = $_POST['wisdom'];
    $lck = $_POST['luck'];

    $uploadOk = 1;
  	$imageName = $_FILES['item-icon']['name'];
  	$errorMessage = "";

  	if($imageName != "")
    {
  		$targetDir = "/img/uploads/items/";
  		$imageName = $targetDir . uniqid() . basename($imageName);
  		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

  		if($_FILES['item-icon']['size'] > 10000000)
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
  			if(move_uploaded_file($_FILES['item-icon']['tmp_name'], $imageName)) {
  				//image uploaded okay
  			}
  			else {
  				//image did not upload
  				$uploadOk = 0;
  			}
  		}

  	}

    if($uploadOk) {
      $item->addNewItem($item_name, $item_type, $item_desc, $item_price, $imageName, $equip_zone, $req_level, $str, $int, $wpr, $agt, $spd, $end, $per, $wsd, $lck, $userLoggedIn);
    }
    else {
      echo "<div style='text-align: center; padding: 2px;' class='alert alert-danger'>
              $errorMessage
            </div>";
    }




  }

//user obj
$user_obj = new User($connect_social, $userLoggedIn);
$num_requests = $user_obj->getNumberOfFriendRequests();
$style = $user_obj->getUserStyle();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  	<title>Above</title>
    <meta name="viewport" content="width=device-width, initial-scale=0.5">

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
  <div id="container2">
    <header>Above Zeniea</header>
    <div id="above" class="boxes">

      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#site_stats" aria-controls="site_stats" data-toggle="tab" aria-selected="true">Site Statistics</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#manage_quests" aria-controls="manage_quests" data-toggle="tab" aria-selected="true">Quests</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#new_post" aria-controls="new_post" data-toggle="tab" aria-selected="true">Posts</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#manage_stores" aria-controls="manage_stores" data-toggle="tab" aria-selected="true">Stores</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#manage_users" aria-controls="manage_users" data-toggle="tab" aria-selected="true">Users</a>
        </li>

      </ul>

      <div class="tab-content" id="myTabContent">
          <div role="tabpanel" class="tab-pane fade show active" id="site_stats" aria-labelledby="site_stats">
            <div class="article">

                <strong>Welcome, <?php echo $userLoggedIn; ?></strong><br />

                <?php

                $posts_data_query = mysqli_query($connect_social, "SELECT * FROM posts WHERE user_closed='no' AND deleted='no'");
                $posts_count = mysqli_num_rows($posts_data_query);
                printf("%d active posts <br />",$posts_count);
                $users_data_query = mysqli_query($connect_social, "SELECT username FROM users WHERE user_closed='no'");
                $users_count = mysqli_num_rows($users_data_query);
                printf("%d active users <br />",$users_count);
                $comments_data_query = mysqli_query($connect_social, "SELECT * FROM comments WHERE removed='no'");
                $comments_count = mysqli_num_rows($comments_data_query);
                printf("%d post comments <br />", $comments_count);

                ?>
                No available quests stats.<br />
                No available store stats. <br /><br />

                <strong>Current Trends:</strong>
                <ul style="padding: 0px; margin: 0px;">
                  <?php
                  $query = mysqli_query($connect_social, "SELECT * FROM trends ORDER BY hits DESC LIMIT 10");

                  foreach ($query as $row) {
                    $word = $row['title'];
                    $word_dot = strlen($word) >= 14 ? "..." : "";
                    $trimmed_word = str_split($word, 14);
                    $trimmed_word = $trimmed_word[0];
                    echo "<li>";
                    echo $trimmed_word . $word_dot;
                    echo "</li>";
                  }
                   ?>
                 </ul>


            </div>
          </div>

<!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

          <div role="tabpanel" class="tab-pane fade" id="new_post" aria-labelledby="new_post">
              <div class="article2">
                <p>This area will be used as a way for admins and moderators to post new updates on the <a href="/?news">news section</a>. Date is automatically filled in.
                <br />
                <form class="post_form" action="/above/" method="POST" autocomplete="off">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">@</span>
                    </div>
                    <input type="text" class="form-control" name="added_by" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo $userLoggedIn; ?>" readonly>
                  </div>
                  <div class="form-group">
                    <select class="form-control" id="exampleFormControlSelect1">
                      <option>Site News</option>
                      <option>Discussion Thread</option>
                      <option>IO Article</option>
                      <option>Legacy Content</option>
                      <option>Global Announcement</option>
                    </select>
                  </div>
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="inputGroup-sizing-default">Title</span>
                    </div>
                    <input type="text" class="form-control" name="post_title" id="post_title" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                  </div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Content</span>
                    </div>
                    <textarea class="form-control" name="post_text" id="post_text" aria-label="With textarea"></textarea>
                  </div>
                  <br />
                  <button type="submit" class="btn btn-post btn-lg btn-block" name="news_post" id="post_button" value="POST">Submit News Post</button>
                </form>

              </div>
          </div>

<!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

          <div role="tabpanel" class="tab-pane fade" id="manage_users" aria-labelledby="manage_users">
            <div class="article">
              <?php
              while ($users_list=mysqli_fetch_array($users_data_query)) {
                  print_r($users_list);
              } ?>
            </div>
          </div>

<!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

          <div role="tabpanel" class="tab-pane fade" id="manage_quests" aria-labelledby="manage_quests">
            <div class="titleBar">Quests</div>
          </div>

<!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

          <div role="tabpanel" class="tab-pane fade" id="manage_stores" aria-labelledby="manage_stores">

            <div class="nav nav-pills" id="stores_sublist" role="tablist" aria-orientation="vertical">
              <a class="nav-link active" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="new_items" aria-selected="true">New Item</a>
              <a class="nav-link" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="edit_items" aria-selected="false">Edit Items</a>
              <a class="nav-link" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="money_supply" aria-selected="false">Money Supply</a>
            </div>

              <div class="tab-content" id="stores_subcontent">
                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="new_items">
                  <div class="article2">
                  <h4>Add New Item:</h4>

                  <form class="post_form" action="/above/" method="POST" autocomplete="off" enctype="multipart/form-data">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">Name</span>
                    </div>
                    <input type="text" name="item-name" class="form-control" aria-label="Name" aria-describedby="basic-addon1" required>
                  </div>

                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">Item Type</span>
                    </div>
                    <select name="item-type" class="form-control" id="exampleFormControlSelect1">
                      <option>1</option>
                      <option>2</option>
                      <option>3</option>
                      <option>4</option>
                      <option>5</option>
                    </select>
                  </div>

                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">Equip Zone</span>
                    </div>
                    <select name="equip-zone" class="form-control" id="exampleFormControlSelect1">
                      <option>bag</option>
                      <option>hand</option>
                      <option>torso</option>
                      <option>legs</option>
                      <option>head</option>
                    </select>
                  </div>

                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Description</span>
                    </div>
                    <textarea name="item-desc" class="form-control" aria-label="With textarea" required></textarea>
                  </div>

                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Gold</span>
                    </div>
                    <input type="text" name="item-price" class="form-control" aria-label="Amount" required>
                    <div class="input-group-append">
                      <span class="input-group-text">.00</span>
                    </div>
                  </div>

                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="inputGroup-sizing-sm">Required Level</span>
                    </div>
                    <input type="text" name="req-level" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" required>
                  </div>

                  <div class="input-group mb-3">
                    <div class="custom-file">
                      <input type="file" name="item-icon" class="custom-file-input" id="newIconImage">
                      <label class="custom-file-label" for="newIconImage">Icon Image</label>
                    </div>
                    <div class="input-group-append">
                      <span class="input-group-text" id="">Upload</span>
                    </div>
                  </div>

                  <div class="input-group input-group-sm"><div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Strength</span></div>
                    <input type="text" name="strength" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  </div>
                  <div class="input-group input-group-sm"><div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Intelligence</span></div>
                    <input type="text" name="intelligence" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  </div>
                  <div class="input-group input-group-sm"><div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Willpower</span></div>
                    <input type="text" name="willpower" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  </div>
                  <div class="input-group input-group-sm"><div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Agility</span></div>
                    <input type="text" name="agility" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  </div>
                  <div class="input-group input-group-sm"><div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Speed</span></div>
                    <input type="text" name="speed" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  </div>
                  <div class="input-group input-group-sm"><div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Endurance</span></div>
                    <input type="text" name="endurance" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  </div>
                  <div class="input-group input-group-sm"><div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Personality</span></div>
                    <input type="text" name="personality" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  </div>
                  <div class="input-group input-group-sm"><div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Wisdom</span></div>
                    <input type="text" name="wisdom" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  </div>
                  <div class="input-group input-group-sm mb-3"><div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Luck</span></div>
                    <input type="text" name="luck" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                  </div>

                  <button type="submit" class="btn btn-post btn-lg btn-block" name="new_item" id="new_item" value="POST">Submit New Item</button>
                </form>


                </div>
                </div>
                  <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="edit_items"></div>
                  <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="money_supply"></div>
              </div>

          </div>

<!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
      </div>
    </div>
  </div>
</body>
</html>
