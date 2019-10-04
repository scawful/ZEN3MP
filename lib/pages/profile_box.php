<div id="profileBox" class="boxes">

     <?php
     if(isset($_SESSION['username'])) {
        echo "<div class='titleBar'>User</div><div class='column'><img class=avatar src=" . $user['avatar'] . ">";
        echo "<a href='/". $user['username'] . "'><strong>" . $user['displayname'] . " </strong></a>";
        echo "<span class='badge badge-dark'>" . $user['user_title']. "</span>";
        echo "<br>Posts: " . $user['num_posts'];
        echo "<br>Likes: " . $user['num_likes'];
        echo $character->dispCharacterStats();
        echo "</div>";

      }
      else
        echo "Not Logged In";
      ?>

</div>
