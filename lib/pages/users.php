<div id="mainBox" class="boxes">
  <div class="titleBar">Active Users</div>
<?php
$usersListQuery = mysqli_query($connect_social, "SELECT * FROM users WHERE user_closed='no' ORDER BY id ASC");

echo '<div class="row" style="justify-content: flex-start !important;">';

while($row = mysqli_fetch_array($usersListQuery, MYSQLI_ASSOC)) {

    $num_friends_ulist = (substr_count($row['friend_list'], ",")) - 1;
    echo '<div class="card" style="width: 20rem; border: 1px solid black; margin: 2px;  overflow: hidden; flex-grow: 1;">
          <img class="card-img-top" src="' . $row['header_img'] . '" alt="Card image cap" style="height: 185px;">
          <div class="card-body">
          <img src="' . $row['avatar'] . '" class="avatar">
          <h5 class="card-title"><a href="' . $row['username'] . '">' . $row['username'] . '</a></h5>
          <p class="card-text">Rank: '. $row['user_title'] . '
          <br>Posts: ' . $row['num_posts'] . '
          <br>Likes: ' . $row['num_likes'] . '
          <br>Friends: ' . $num_friends_ulist . '
          <br>Date Joined: ' . $row['signup_date'] . '</p>
          </div>
          </div>
          ';
}
mysqli_free_result ($usersListQuery);
echo '</div>';


?>
</div>

<div id="profileBox" class="boxes">
  <div class="titleBar">Statistics</div>

</div>

<div id="sideBox" class="boxes">
  <div class="titleBar">Ads served by Google</div>
  <br />
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- TallBoi -->
  <ins class="adsbygoogle"
       style="display:block"
       data-ad-client="ca-pub-8036786549441922"
       data-ad-slot="1140152702"
       data-ad-format="auto"
       data-full-width-responsive="true"></ins>
  <script>
       (adsbygoogle = window.adsbygoogle || []).push({});
  </script>
</div>
