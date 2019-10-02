<?php
if(isset($_POST['update_details'])) {

  $displayname = strip_tags($_POST['displayname']);
  $email = strip_tags($_POST['email']);
  $header_image = strip_tags($_POST['header_image']);
  $avatar = strip_tags($_POST['avatar']);

  $user_check = mysqli_query($connect_social, "SELECT * FROM users WHERE username='$userLoggedIn'");
  $row = mysqli_fetch_array($user_check);
  $matched_user = $row['username'];

  if($matched_user == "" || $matched_user == $userLoggedIn) {
      $message = "Details updated!<br>";

      $query = mysqli_query($connect_social, "UPDATE users SET displayname='$displayname', email='$email', header_img='$header_image', avatar='$avatar' WHERE username='$userLoggedIn'");
  }
  else
      $message = "That email is already in use!<br>";

}
else
    $message = "";


//*****************************************

if(isset($_POST['update_about'])) {

  $update_bio = strip_tags($_POST['userBio']);
  $update_website = strip_tags($_POST['userWebsite']);
  $update_twitter = strip_tags($_POST['userTwitter']);
  $update_youtube = strip_tags($_POST['userYouTube']);
  $update_discord = strip_tags($_POST['userDiscord']);
  $update_github = strip_tags($_POST['userGithub']);
  $update_switch = strip_tags($_POST['userSwitch']);

  $user_check = mysqli_query($connect_social, "SELECT * FROM users WHERE username='$userLoggedIn'");
  $row = mysqli_fetch_array($user_check);
  $user_id = $row['id'];

  $query = mysqli_query($connect_social, "UPDATE users_about SET bio='$update_bio', website='$update_website', twitter='$update_twitter', discord='$update_discord', youtube='$update_youtube', github='$update_github', switch='$update_switch' WHERE user_id='$user_id'");

  header("Location: https://zeniea.com/" . $userLoggedIn );

}

//*****************************************

if(isset($_POST['update_style'])) {

  $update_style = $_POST['style_change'];

  $user_check = mysqli_query($connect_social, "SELECT * FROM users WHERE username='$userLoggedIn'");
  $row = mysqli_fetch_array($user_check);
  $user_id = $row['id'];

  $query = mysqli_query($connect_social, "UPDATE users_about SET style='$update_style' WHERE user_id='$user_id'");

  header("Location: https://zeniea.com/" . $userLoggedIn );

}


//*****************************************

if(isset($_POST['update_password'])) {

  $old_password = strip_tags($_POST['oldpassword']);
  $new_password = strip_tags($_POST['newpassword']);
  $new_password2 = strip_tags($_POST['newpassword2']);

  $password_query = mysqli_query($connect_social, "SELECT password FROM users WHERE username='$userLoggedIn'");
  $row = mysqli_fetch_array($password_query);
  $db_password = $row['password'];

  if (password_verify($old_password, $db_password)) {

      if($new_password == $new_password2) {

          if(strlen($new_password) <= 4) {
              $password_message = "Sorry, your password must be greater than 4 characters<br>";
          }
          else {
              $new_password_hash = password_hash($new_password,PASSWORD_DEFAULT);
              $password_query = mysqli_query($connect_social, "UPDATE users SET password='$new_password_hash' WHERE username='$userLogged'");
              $password_message = "Password has been changed!<br>";
          }

      }
      else {
          $password_message = "Passwords do not match<br>";
      }

  }
  else {
      $password_message = "Old password is incorrect!<br>";
  }



}
else {
  $password_message = "";
}

if(isset($_POST['close_account'])) {
    $close_query = mysqli_query($connect_social, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
    session_destroy();
    header("Location: https://zeniea.com");
}


?>
