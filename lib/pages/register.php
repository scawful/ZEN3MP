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
<div id="mainBox" class="boxes">
  <div class="article">

		<div class="card">
		  <div class="card-body">
		    <h5>This website is currently under heavy development, but is accepting new members.</h5>
				Zeniea operates like most social media platforms, with the added feature of giving you a custom character to develop. Over time I will add a variety of rpg-esque features to the site such as:
				<ul>
					<li>Posting reward system (experience, gold)</li>
					<li>Comprehensive Item Store (weapons, gear)</li>
					<li>Interactive webcomic style questing</li>
					<li>P.V.P. turn-based battles</li>
					<li>And much more! (I hope)</li>
				</ul>
				Right now you can go ahead and register for an account and create a character with the verification link provided to you in the e-mail.
				<br>For updates on progress check the <a href="?news">site news</a> section.
		  </div>
		</div>
		<h2>Projects:</h2>

		<div class="card">
		  <div class="card-body">
					<a href="?zelda">
					<img src="/img/icons/link_triforce.png" height="60px" align="left">
						<h4>Legend of Zelda: Oracle of Secrets</h4></a>
						Rom hack of Link to the Past for the Super Nintendo.

			</div>
		 </div>
	</div>
</div>

	<div id="profileBox" class="boxes">

					<div class='titleBar'>Welcome to Zeniea</div>
						<div id='first'>


							<!-- Login Form -->
							<form class="px-4 py-3" action="" method="POST">
								<div class='form-group'>
									<input type='email' name="login_email" class='form-control' placeholder="Email Address" value='<?php
						      if(isset($_SESSION['login_email'])) {
						        echo $_SESSION['login_email'];
						      }
						      ?>' required>
								</div>
								<div class='form-group'>
									<input type='password' name="login_password" class='form-control' placeholder='Password'>
								</div>
								<?php if(in_array("Email or password was incorrect<br>", $error_array)) echo  "Email or password was incorrect<br>"; ?>
								<?php if(in_array("UserNotVerified", $error_array)) echo  "Your account has not been verified!<br>
																																							<input class='btn btn-primary' type='submit' name='resend' Value='Resend Verification Email'>
																																							<br>"; ?>
								<!-- <div class='form-check'>
									<input type='checkbox' class='form-check-input' id='dropdownCheck'>
									<label class='form-check-label' for='dropdownCheck'>
										Remember me
									</label>
								</div>-->
								<button type='submit' name="login_button" class='btn btn-primary' value="Login">Sign in</button>

							</form>
							<div class='dropdown-divider'></div>
							<a class='dropdown-item' href='#' id='signup' class='signup'>Need an account? Sign up.</a>
							<a class='dropdown-item' href='#'>Forgot password?</a>
						</div>

						<!------------------------------------------>


						<div id='second'>
						<form class='px-4 py-3'>
						<div class='form-group'>
							<input type='username' class='form-control' id='register_username' placeholder="Username" value="<?php
							if(isset($_SESSION['reg_uname'])) {
								echo $_SESSION['reg_uname'];
							}
							?>" required>
						</div>
						<?php if(in_array("Your username must be between 2 and 25 characters<br>", $error_array)) echo "Your username must be between 2 and 25 characters<br>"; ?>

							<div class='form-group'>
								<input type='email' class='form-control' id='exampleDropdownFormEmail1' placeholder="Email" value="<?php
								if(isset($_SESSION['reg_email'])) {
									echo $_SESSION['reg_email'];
								}
								?>" required>
							</div>
							<div class='form-group'>
								<input type='email' class='form-control' id='exampleDropdownFormEmail2' placeholder="Confirm Email" value="<?php
								if(isset($_SESSION['reg_email2'])) {
									echo $_SESSION['reg_email2'];
								}
								?>" required>
							</div>
							<?php if(in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>";
							else if(in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>";
							else if(in_array("Emails don't match<br>", $error_array)) echo "Emails don't match<br>"; ?>

							<div class='form-group'>
								<input type='password' class='form-control' id='exampleDropdownFormPassword1' placeholder='Password' required>
							</div>
							<div class='form-group'>
								<input type='password' class='form-control' id='exampleDropdownFormPassword2' placeholder='Confirm Password' required>
							</div>
							<?php if(in_array("Your passwords do not match<br>", $error_array)) echo "Your passwords do not match<br>";
							else if(in_array("Your password can only contain english characters or numbers<br>", $error_array)) echo "Your password can only contain english characters or numbers<br>";
							else if(in_array("Your password must be betwen 5 and 30 characters<br>", $error_array)) echo "Your password must be betwen 5 and 30 characters<br>"; ?>


							<div class='g-recaptcha' data-sitekey='6LdNwpoUAAAAANq-7GkziNyXo5hVqO1bNeqWXaHF'></div>
							<br />
							<button type="submit" name="register_button" class='btn btn-primary'>Register</button>
							</form>
							<?php if(in_array("<span style='color: #14C800;'>You're all set! Wait for the email to complete the registration process. </span><br>", $error_array)) echo "<span style='color: #14C800;'>You're all set! Wait for the email to complete the registration process. </span><br>"; ?>

							<div class='dropdown-divider'></div>
							<a class='dropdown-item' href='#' id='signin' class='signin'>Already have an account? Sign in here!</a>


						</div>
					</div>
