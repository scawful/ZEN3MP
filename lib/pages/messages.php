<div id="mainBox" class="boxes">
<?php

$message_obj = new Message($connect_social, $userLoggedIn, $spdo);

if(isset($_GET['u']))
    $user_to = $_GET['u'];
else {
    $user_to = $message_obj->getMostRecentUser();
    if($user_to == false)
        $user_to = 'new';
}

if(isset($_POST['post_message'])) {

    if(isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($connect_social, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
    }

}

if($user_to != "new") {
    $user_to_obj = new User($user_to, $spdo);
    echo "<div class='article'>";
    echo "<h4> You and <a href='$user_to'>" . $user_to_obj->getDisplayName() . "</a></h4><hr><br/>";
    echo "<div class='loaded_messages' id='scroll_messages'>";
        echo $message_obj->getMessages($user_to);
        echo "</div>";
} else {
    echo "<br>";
}



?>

  <form class="" action="" method="POST">
      <?php

      if($user_to == "new") {
          echo "Select the friend you would like to message. <br><br>";
          ?>
          To: <input type='text' onkeyup='getUser(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'></input>
          <?php
          echo "<div class='results'></div>";

      }
      else {
          echo "
          <div class='input-group'>
            <textarea class='form-control' name='message_body' aria-label='With textarea'></textarea>
            <div class='input-group-append'>
              <button type='submit' name='post_message' class='btn btn-outline-secondary' id='message_submit type='button'>Send</button>
            </div>
          </div>
          <br>


          ";
          echo '<button type="button" class="btn btn-danger btn-lg btn-block">Challenge to a battle!</button>
                <button type="button" class="btn btn-info btn-lg btn-block">Offer to trade.</button>
                </div>';
      }
      ?>
  </form>
  <script>

    var div = document.getElementById("scroll_messages");
    div.scrollTop = div.scrollHeight;

  </script>


</div> <!—- End MainBox -—>
<div id="sideBox" class="boxes">
  <div class="titleBar">Conversations</div>
    <div class="column list-group">
      <?php echo $message_obj->getConvos(); ?>
      <div style="text-align: center; margin-top: 10px;">
        <a href="?inbox&u=new"><button class="btn btn-primary">New Message</button></a>
      </div>
    </div>
</div>
