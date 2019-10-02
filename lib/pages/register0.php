<?php
include 'lib/config.php';
include 'lib/forms/register_form.php';
include 'lib/forms/login_form.php';

	if(isset($_POST['register_button'])) {
		echo '
		<script>

		$(document).ready(function() {
			$("#first").hide();
			$("#second").show();
		});

		</script>

		';
	}
	?>

  <div class="article">
    <div class="login_box">
			<div class="login_header">
        <h1>Welcome to Zeniea</h1>
				Before you get started, make sure to check out the rules, privacy policy, and frequently asked questions!
      </div>
        <div id="first">

    <!-- Login Form -->
    <form class="login" action="" method="POST">
      <input type="email" name="login_email" placeholder="Email Address" value="<?php
      if(isset($_SESSION['login_email'])) {
        echo $_SESSION['login_email'];
      }
      ?>" required>
      <br>
      <input type="password" name="login_password" placeholder="Password">
      <br>
      <?php if(in_array("Email or password was incorrect<br>", $error_array)) echo  "Email or password was incorrect<br>"; ?>
			<?php if(in_array("UserNotVerified", $error_array)) echo  "Your account has not been verified!<br>
																																		<input class='btn btn-primary' type='submit' name='resend' Value='Resend Verification Email'>
																																		<br>"; ?>
      <input type="submit" name="login_button" value="Login">
      <br>
      <a href="#" id="signup" class="signup">Need an account? Register here!</a>

    </form>
  </div>

<div id="second">
  <!-- Registration Form  //  -->
	<form class="register" action="" method="POST">
		<input type="text" name="reg_uname" placeholder="Username" value="<?php
		if(isset($_SESSION['reg_uname'])) {
			echo $_SESSION['reg_uname'];
		}
		?>" required>
		<br>
		<?php if(in_array("Your username must be between 2 and 25 characters<br>", $error_array)) echo "Your username must be between 2 and 25 characters<br>"; ?>


		<input type="email" name="reg_email" placeholder="Email" value="<?php
		if(isset($_SESSION['reg_email'])) {
			echo $_SESSION['reg_email'];
		}
		?>" required>
		<br>

		<input type="email" name="reg_email2" placeholder="Confirm Email" value="<?php
		if(isset($_SESSION['reg_email2'])) {
			echo $_SESSION['reg_email2'];
		}
		?>" required>
		<br>
		<?php if(in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>";
		else if(in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>";
		else if(in_array("Emails don't match<br>", $error_array)) echo "Emails don't match<br>"; ?>


		<input type="password" name="reg_password" placeholder="Password" required>
		<br>
		<input type="password" name="reg_password2" placeholder="Confirm Password" required>
		<br>
		<?php if(in_array("Your passwords do not match<br>", $error_array)) echo "Your passwords do not match<br>";
		else if(in_array("Your password can only contain english characters or numbers<br>", $error_array)) echo "Your password can only contain english characters or numbers<br>";
		else if(in_array("Your password must be betwen 5 and 30 characters<br>", $error_array)) echo "Your password must be betwen 5 and 30 characters<br>"; ?>

		<div class="g-recaptcha" data-sitekey="6LdNwpoUAAAAANq-7GkziNyXo5hVqO1bNeqWXaHF"></div>
		<input type="submit" name="register_button" value="Register">
		<br>

    <?php if(in_array("<span style='color: #14C800;'>You're all set! Wait for the email to complete the registration process. </span><br>", $error_array)) echo "<span style='color: #14C800;'>You're all set! Wait for the email to complete the registration process. </span><br>"; ?>
    <a href="#" id="signin" class="signin">Already have an account? Sign in here!</a>
	</form>
</div>
</div>
</div>
</div>

	<div id="profileBox" class="boxes">

					<div class='titleBar'>Welcome to Zeniea</div>
						<div id='first'>
							<!--
							<form class='px-4 py-3'>
								<div class='form-group'>
									<input type='email' class='form-control' id='exampleDropdownFormEmail1' placeholder='Email Address'>
								</div>
								<div class='form-group'>
									<input type='password' class='form-control' id='exampleDropdownFormPassword1' placeholder='Password'>
								</div>
								<div class='form-check'>
									<input type='checkbox' class='form-check-input' id='dropdownCheck'>
									<label class='form-check-label' for='dropdownCheck'>
										Remember me
									</label>
								</div>
								<button type='submit' class='btn btn-primary'>Sign in</button>
							</form>
							<div class='dropdown-divider'></div>
							<a class='dropdown-item' href='#' id='signup' class='signup'>New around here? Sign up</a>
							<a class='dropdown-item' href='#'>Forgot password?</a>
						</div>

						<div id='second'>
						<form class='px-4 py-3'>
						<div class='form-group'>
							<input type='username' class='form-control' id='register_username' placeholder='Username'>
						</div>
							<div class='form-group'>
								<input type='email' class='form-control' id='exampleDropdownFormEmail1' placeholder='Email Address'>
							</div>
							<div class='form-group'>
								<input type='password' class='form-control' id='exampleDropdownFormPassword1' placeholder='Password'>
							</div>
							<div class='form-check'>
								<input type='checkbox' class='form-check-input' id='dropdownCheck'>
								<label class='form-check-label' for='dropdownCheck'>
									Remember me
								</label>
							</div>
							<div class='g-recaptcha' data-sitekey='6LdNwpoUAAAAANq-7GkziNyXo5hVqO1bNeqWXaHF'></div>
							<button type='submit' class='btn btn-primary'>Sign in</button>
							</form>
							<div class='dropdown-divider'></div>
							<a class='dropdown-item' href='#' id='signin' class='signin'>Already have an account? Sign in here!</a>
						-->

						</div>
						<br />

					</div>
