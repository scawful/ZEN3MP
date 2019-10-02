<div id="mainBox" class="boxes">

<?php include("lib/forms/settings_form.php"); ?>
<br>
<div class="card">
  <form class="" action="" method="POST" style="text-align: left; margin: 10px;">

    <?php
    $user_data_query = mysqli_query($connect_social, "SELECT id, displayname, email, header_img, avatar FROM users WHERE username='$userLoggedIn'");
    $row = mysqli_fetch_array($user_data_query);

    $displayname = $row['displayname'];
    $email = $row['email'];
    $header_image = $row['header_img'];
    $avatar = $row['avatar'];
    $user_id = $row['id'];

    $user_about_query = mysqli_query($connect_social, "SELECT * FROM users_about WHERE user_id='$user_id'");
    $row_about = mysqli_fetch_array($user_about_query);

    $userBio = $row_about['bio'];
    $userWebsite = $row_about['website'];
    $userTwitter = $row_about['twitter'];
    $userYouTube = $row_about['youtube'];
    $userDiscord = $row_about['discord'];
    $userGithub = $row_about['github'];
    $userSwitch = $row_about['switch'];
    $userStyle = $row_about['style'];

     ?>

    <h4>Modify User Details:</h4>
    <div class="form-group">
      <label for="formGroupExampleInput">Display Name:</label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $displayname; ?>" name="displayname"  style="width: 300px;">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput2">E-Mail: </label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $email; ?>" name="email"  style="width: 300px;">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput2">Avatar: (Temporary Solution) </label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $avatar; ?>" name="avatar" style="width: 300px;">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput2">Header Image: (Temporary Solution) </label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $header_image; ?>" name="header_image" style="width: 300px;">
    </div>

    <?php echo $message; ?>
    <button type="submit" name="update_details" id="update_details" class="btn btn-primary">Submit</button>
  </form>
  <br>

  <form action="" method="POST" style="text-align: left; margin: 10px;">

    <h4>Modify User About:</h4>
    <div class="form-group">
      <label for="formGroupExampleInput">Bio: *stripped tags until i add markdown*</label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $userBio; ?>" name="userBio" style="width: 600px;">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput">Website: </label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $userWebsite; ?>" name="userWebsite"  style="width: 300px;">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput2">Twitter: </label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $userTwitter; ?>" name="userTwitter"  style="width: 300px;">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput2">YouTube: </label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $userYouTube; ?>" name="userYouTube" style="width: 300px;">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput2">Discord: </label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $userDiscord; ?>" name="userDiscord" style="width: 300px;">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput2">Github: </label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $userGithub; ?>" name="userGithub" style="width: 300px;">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput2">Nintendo Switch: </label>
      <input type="text" class="form-control" id="formGroupExampleInput" value="<?php echo $userSwitch; ?>" name="userSwitch" style="width: 300px;">
    </div>

    <button type="submit" name="update_about" id="update_details" class="btn btn-primary">Submit</button>

  </form>
  <br>

  <form class="" action="" method="POST" style="text-align: left; margin: 10px;">

    <div class="form-group">
    <label for="exampleFormControlSelect1">Select Style</label>
    <select class="form-control" id="style_change" name="style_change">
      <option <?php if ($userStyle == 'purpleStyle') echo 'selected="selected"'; ?> value="purpleStyle">Zeniea Purple</option>
      <option <?php if ($userStyle == 'darkSky') echo 'selected="selected"'; ?> value="darkSky">Darksky [Legacy]</option>
      <option <?php if ($userStyle == 'themes/acidtech') echo 'selected="selected"'; ?> value="themes/acidtech">AcidTech (Incomplete)</option>
      <option <?php if ($userStyle == 'themes/pinkie') echo 'selected="selected"'; ?> value="themes/pinkie">Pinkie (Incomplete)</option>
      <option <?php if ($userStyle == 'themes/proSilver') echo 'selected="selected"'; ?> value="proSilver">ProSilver (Incomplete)</option>
      <option <?php if ($userStyle == 'themes/avalon') echo 'selected="selected"'; ?> value="themes/avalon">Avalon (Incomplete)</option>
      <option <?php if ($userStyle == 'themes/lightpurple') echo 'selected="selected"'; ?> value="themes/lightpurple">Light Purple (Incomplete)</option>
    </select>
  </div>
  <button type="submit" name="update_style" id="update_style" class="btn btn-primary">Submit</button>

  </form>


  <form class="" action="" method="POST" style="text-align: left; margin: 10px;">
    <h4>Reset Password:</h4>
    <div class="form-group">
      <label for="formGroupExampleInput3">Old Password: </label>
      <input type="password" class="form-control" id="formGroupExampleInput" name="oldpassword">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput4">New Password: </label>
      <input type="password" class="form-control" id="formGroupExampleInput" name="newpassword">
    </div>
    <div class="form-group">
      <label for="formGroupExampleInput5">New Password Again: </label>
      <input type="password" class="form-control" id="formGroupExampleInput" name="newpassword2">
    </div>

    <?php echo $password_message; ?>


    <button type="submit" name="update_password" id="update_password" class="btn btn-primary">Reset</button>
  </form>

<hr>
  <form class="" action="" method="POST" style="text-align: left; margin: 5px;">
    <h4>Close Account</h4>
    <p>This button closes your account from being seen. Log back in to reactivate your account, does not delete any of your data.</p>
    <div class="form-group">
      <input type="submit" class="btn btn-danger" id="formGroupExampleInput" name="close_account" value="Close Account">
    </div>
  </form>

</div>
</div>
