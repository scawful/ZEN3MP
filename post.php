<?php
include("lib/header.php");

if(isset($_GET['id'])) {
  $id = $_GET['id'];
}
else {
  $id = 0;
}
?>
<div id="mainBox" class="boxes">
  <div class="titleBar">Displaying Post</div>
  <br />
  <div class="posts_area">

    <?php
        $post = new Post($connect_social, $userLoggedIn, $spdo);
        $post->getSinglePost($id);
    ?>
  </div>

</div>

<div id="profileBox" class="boxes">
   <div class="column">
     <?php
     if(isset($_SESSION['username'])) {
        echo "<img class=avatar src=" . $user['avatar'] . ">";
        echo "<strong>" . $user['username'] . "</strong>";
        echo "<br>Rank: " . $user['user_title'];
        echo "<br>Posts: " . $user['num_posts'];
        echo "<br>Likes: " . $user['num_likes'];
        echo "<br>Friends: " . $num_friends . "";

      } else {
        
      }
     ?>
   </div>
</div>



  <div id="sideBox" class="boxes">

  </div>
<?php include("lib/footer.php"); ?>
