<?php
include ('lib/header.php');
if(isset($_GET['profile_username'])) {
  $username = $_GET['profile_username'];
  $user_details_query = mysqli_query($connect_social, "SELECT * FROM users WHERE username='$username'");
  $user_array = mysqli_fetch_array($user_details_query);

  $num_friends = (substr_count($user_array['friend_list'], ",")) - 1;
}

if(isset($_POST['remove_friend'])) {
  $user = new User($userLoggedIn, $spdo);
  $user->removeFriend($username);
}

if(isset($_POST['add_friend'])) {
  $user = new User($userLoggedIn, $spdo);
  $user->sendRequest($username);
}

if(isset($_POST['respond_request'])) {
  header("Location: requests.php");
}

$message_obj = new Message($connect_social, $userLoggedIn, $spdo);
$profile_user_obj = new User($username, $spdo);


if(isset($_POST['post_message'])) {
  if(isset($_POST['message_body'])) {
    $body = mysqli_real_escape_string($connect_social, $_POST['message_body']);
    $date = date("Y-m-d H:i:s");
    $message_obj->sendMessage($username, $body, $date);
  }
  $link = '#profileTabs a[href="#messages_div"]';
  echo "<script>
          $(function() {
              $('" . $link . "').tab('show');
          });
        </script>";
}

?>
<script>
$(function(){

         var userLoggedIn = '<?php echo $userLoggedIn; ?>';
         var profileUsername = '<?php echo $username; ?>';
         var inProgress = false;

         loadPosts(); //Load first posts

         $(window).scroll(function() {
             var bottomElement = $(".status_post").last();
             var noMorePosts = $('.posts_area').find('.noMorePosts').val();

             // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
             if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
                loadPosts();
            }
         });

         function loadPosts() {
             if(inProgress) { //If it is already in the process of loading some posts, just return
               return;
             }

             inProgress = true;
             $('#loading').show();

             var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

             $.ajax({
                 url: "lib/forms/ajax_load_profile_posts.php",
                 type: "POST",
                 data: "page="+page+"&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                 cache:false,

                 success: function(response) {
                     $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                     $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
                     $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage

                     $('#loading').hide();
                     $(".posts_area").append(response);

                     inProgress = false;
                 }
             });
         }

         //Check if the element is in view
         function isElementInView (el) {
             if(el == null) {
                 return;
             }

             var rect = el.getBoundingClientRect();

             return (
                 rect.top >= 0 &&
                 rect.left >= 0 &&
                 rect.bottom <= $(window).height() &&
                 rect.right <= $(window).width()
                 //rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                 //rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
             );
         }
     });
 </script>


<!-- <script>
var userLoggedIn = '<?php echo $userLoggedIn; ?>';
var profileUsername = '<?php echo $username; ?>';

$(document).ready(function() {

  $('#loading').show();

  //Original ajax request for loading first posts
  $.ajax({
    url: "lib/forms/ajax_load_profile_posts.php",
    type: "POST",
    data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
    cache:false,

    success: function(data) {
      $('#loading').hide();
      $('.posts_area').html(data);
    }
  });

  $(window).scroll(function() {
    var height = $('.posts_area').height(); //Div containing posts
    var scroll_top = $(this).scrollTop();
    var page = $('.posts_area').find('.nextPage').val();
    var noMorePosts = $('.posts_area').find('.noMorePosts').val();

    if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
      $('#loading').show();

      var ajaxReq = $.ajax({
        url: "lib/forms/ajax_load_profile_posts.php",
        type: "POST",
        data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
        cache:false,

        success: function(response) {
          $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
          $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage

          $('#loading').hide();
          $('.posts_area').append(response);
        }
      });

    } //End if

    return false;

  }); //End (window).scroll(function())


});

</script> -->
    <div id="mainBox" class="boxes">


      <div class="profile_header" style="background-image:url('<?php echo $user_array['header_img']; ?>');">

        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a class="nav-link active" href="#posts_div" aria-controls="posts_div" data-toggle="tab" aria-selected="true">
              &nbsp;<i class="typcn typcn-rss icon btnProfile"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#about_div" aria-controls="about_div" data-toggle="tab" aria-selected="false">
              <i class="typcn typcn-business-card icon btnProfile"></i>About
            </a>
          </li>
          <?php // Check if logged in
          if(isset($_SESSION['username'])) {
            if($userLoggedIn != $username) {
          ?>
          <li class="nav-item">
            <a class="nav-link" href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab" aria-selected="false">
              <i class="typcn typcn-message icon btnProfile"></i>Messages
            </a>
          </li>
        <?php } } ?>
          <!-- <li class="nav-item">
            <a class="nav-link disabled" href="#photos_div" aria-controls="photos_div" role="tab" data-toggle="tab" aria-selected="false">
              <i class="typcn typcn-image icon btnProfile"></i>Photos
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#media_div" aria-controls="media_div" role="tab" data-toggle="tab" aria-selected="false">
              <i class="typcn typcn-notes icon btnProfile"></i>Music
            </a>
          </li> -->
          <?php // Check if logged in
          if(isset($_SESSION['username'])) {
            if($userLoggedIn == $username) {
          ?>
          <li class="nav-item">
            <a class="nav-link" href="#inventory_div" aria-controls="inventory_div" role="tab" data-toggle="tab" aria-selected="false">
              <i class="typcn typcn-briefcase icon btnProfile"></i>Inventory
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#settings_div" aria-controls="settings_div" role="tab" data-toggle="tab" aria-selected="false">
              <i class="typcn typcn-cog icon btnProfile"></i>Settings
            </a>
          </li>
        <?php } } ?>
        </ul>

      </div>


      <div class="tab-content" id="myTabContent">

          <div role="tabpanel" class="tab-pane fade show active" id="posts_div" aria-labelledby="posts_div">
            <div class="titleBar">Displaying Posts</div>
            <br>
            <div class="posts_area">
            </div>
            <img id="loading" src="img/icons/loading.gif">
          </div>

          <div role="tabpanel" class="tab-pane fade" id="about_div" aria-labelledby="about_div">
            <div class="titleBar">About <?php echo $username; ?></div>
              <div class="article">
                <?php $profile_user_obj->getUserAboutDetails(); ?>
              </div>
          </div>

          <div role="tabpanel" class="tab-pane fade" id="photos_div">
            <div class="titleBar">Displaying Photos</div>
          </div>

          <div role="tabpanel" class="tab-pane fade" id="messages_div">
            <?php
                  echo "<div class='article'>";
                  echo "<h4> You and <a href='" . $username . "'>" . $profile_user_obj->getDisplayName() . "</a></h4><hr><br/>";
                  echo "<div class='loaded_messages' id='scroll_messages'>";
                      echo $message_obj->getMessages($username);
                      echo "</div></div>";
              ?>
                <form class="" action="" method="POST">
                    <textarea name='message_body' id='message_textarea' placeholder='Write your message...'></textarea>
                    <button type='submit' name='post_message' class='btn btn-primary' id='message_submit' value='Send'>Send</button>
                </form>

          </div>

          <div role="tabpanel" class="tab-pane fade" id="inventory_div">
            <div class="titleBar">Inventory</div>

            <?php
            $inventory->getPlayerInventory();
             ?>

          </div>

          <div role="tabpanel" class="tab-pane fade" id="spiral_div">

          </div>

          <div role="tabpanel" class="tab-pane fade" id="settings_div">
            <div class="titleBar">Profile Settings</div>
            <?php include("lib/pages/settings.php"); ?>

          </div>



</div>


    </div> <!-- end main box -->

    <div id="sideBox" class="boxes">
      <div class="titleBar">Character</div>
        <div class="article">
        <?php
        $character_obj = new Character($username, $spdo, $rpdo);

        $user_id = $user_array['id'];

        $activeCharId = mysqli_query($connect_rpg, "SELECT character_id FROM user_character WHERE user_id='$user_id' LIMIT 1");
        $user_character_id_array = mysqli_fetch_array($activeCharId);
        $user_character_id = $user_character_id_array['character_id'];

        $getProfCharInfoQuery = mysqli_query($connect_rpg, "SELECT * FROM rpg_character WHERE character_id='$user_character_id'");
        $character_array = mysqli_fetch_array($getProfCharInfoQuery);

        $getProfCharAtrbQuery = mysqli_query($connect_rpg, "SELECT * FROM character_attribute WHERE character_id='$user_character_id'");


        $charHealthBar = "<div class='progress' style='margin-bottom: 5px;'>
                            <div class='progress-bar bg-success' role='progressbar' style='width: 100%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'>Health: " . $character_obj->getCharacterHealth() . "</div>
                          </div>
                          ";

        $charManaBar = "<div class='progress' style='margin-bottom: 5px;'>
                            <div class='progress-bar bg-info' role='progressbar' style='width: 100%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'>Mana: " . $character_array['mana'] . "</div>
                          </div>
                          ";

        $charFatigueBar = "<div class='progress' style='margin-bottom: 5px;'>
                            <div class='progress-bar bg-warning' role='progressbar' style='width: 100%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'>Fatigue: " . $character_array['fatigue'] . "</div>
                          </div>
                          ";

        echo $character_array['name'];
        echo "<br>Level: " . $character_array['level'];
        echo "<br>Race: " . $character_array['race'];
        echo "<br>Class: " . $character_array['class'];
        echo $charHealthBar;
        echo $charManaBar;
        echo $charFatigueBar;
        echo "<div style='float:left;'>Strength<br>Intelligence<br>Willpower<br>Agility<br>Speed<br>Endurance<br>Personality<br>Wisdom<br>Luck</div>";
        while($row = mysqli_fetch_array($getProfCharAtrbQuery)) {

          printf ("<div style='float: right;'> %d</div><br>", $row[3]);

        }



         ?>
     </div>
    </div>

    <div id="profileBox" class="boxes">
      <div class="titleBar">User</div>
    		 <div class="column">
    			 <img src="<?php
           echo $user_array['avatar'];
    			 ?>" class="avatar">
                <?php echo $user_array['displayname']; ?>
            <br><?php echo "Rank: " . $user_array['user_title']; ?>
           <br><?php echo "Posts: " . $user_array['num_posts']; ?>
           <br><?php echo "Likes: " . $user_array['num_likes']; ?>
           <br><?php echo "Friends: " . $num_friends; ?>

          <?php // Check if is logged in
          if(isset($_SESSION['username'])) {
          ?>
            <form class="" action="<?php echo $username; ?>" method="POST">
              <?php

              $profile_user_obj = new User($username, $spdo);

              if($profile_user_obj->isClosed()) {
                header("lib/pages/user_closed.php");
              }

              $logged_in_user_obj = new User($userLoggedIn, $spdo);

              if($userLoggedIn != $username) {

                if($userLoggedIn != $username) {
                  echo 'Mutual Friends: ';
                  echo $logged_in_user_obj->getMutualFriends($username);
                  echo '<br>';
                }

                if($logged_in_user_obj->isFriend($username)) {
                    echo '<input type="submit" name="remove_friend" class="btn btn-danger" value="Remove Friend"><br>';
                }
                else if ($logged_in_user_obj->didReceiveRequest($username)) {
                  echo '<input type="submit" name="respond_request" class="btn-warning" value="Respond to Request"><br>';
                }
                else if ($logged_in_user_obj->didSendRequest($username)) {
                  echo '<input type="submit" name="" class="btn btn-default" value="Request Sent."><br>';
                }
                else
                  echo '<input type="submit" name="add_friend" class="btn btn-success" value="Add Friend."><br>';
              }

               ?>
             </form>
            <p><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#post_form">Post Something</button></p>
          <?php
          }
          ?>
       </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="postModalLabel">Post something!</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>This will appear on the user's profile page and also their newsfeed for your friends to see!</p>

            <form class="profile_post" action="" method="POST">
              <div class="form-group">
                <textarea class="form-control" name="post_body"></textarea>
                <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
                <input type="hidden" name="user_to" value="<?php echo $username; ?>">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
          </div>
        </div>
      </div>
    </div>


    <?php include('lib/footer.php'); ?>



  </body>
</html>
