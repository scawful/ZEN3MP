<?php

if(isset($_POST['login_button'])) {

	$email = filter_var($_POST['login_email'], FILTER_SANITIZE_EMAIL); //sanitize email

	$_SESSION['login_email'] = $email; //Store email into session variable
	$password = $_POST['login_password']; //Get password

	$check_database_query = mysqli_query($connect_social, "SELECT * FROM users WHERE email='$email'");
	$check_login_query = mysqli_num_rows($check_database_query);


	if($row = mysqli_fetch_array($check_database_query)) {

			if($row['verify_user'] == 'yes') {
				if (password_verify($password, $row['password'])) {
					$username = $row['username'];
					// Check if closed, reopen if closed
					$user_closed_query = mysqli_query($connect_social, "SELECT * FROM users WHERE email='$email' AND user_closed='yes'");
					if(mysqli_num_rows($user_closed_query) == 1) {
						$reopen_account = mysqli_query($connect_social, "UPDATE users SET user_closed='no' WHERE email='$email'");
					}
					$_SESSION['username'] = $username;
					header("Location: index.php");
					exit();
				} else {
					array_push($error_array, "Email or password was incorrect<br>");
				}
			} else {
				array_push($error_array, "UserNotVerified");
			}

		} else {
		echo "This user does not exist"; //email entered does not match any in DB
	}

}

if(isset($_POST['resend'])) {

	$email = filter_var($_POST['login_email'], FILTER_SANITIZE_EMAIL); //sanitize email

	$_SESSION['login_email'] = $email; //Store email into session variable

	$check_database_query1 = mysqli_query($connect_social, "SELECT * FROM users WHERE email='$email'");

	$row = mysqli_fetch_array($check_database_query1);
	$uname = $row['username'];

	$verify_hash = md5(rand(0,1000));

	$query = mysqli_query($connect_social, "UPDATE users SET verify_hash='$verify_hash' WHERE email='$email'");

	// Verify User Email

	$to      = $email; // Send email to our user
	$subject = 'Zeniea | Registration Verification and Class Creation'; // Give the email a subject
	$message = '
	Welcome to Zeniea!

	Thanks for signing up, '.$uname.'!
	Your account has been created, however before you can log in you must create your character and decide their class. This acts as the verification process to prevent spammers.

	ZEN3MP is an experimental project so this verification page may change. Eventually there will be an update character page for any legacy accounts caught up in development changes.

	Please click this link to activate your account and set up your character:
	http://www.zeniea.com/?verify&email='.$email.'&key='.$verify_hash.'


	'; // message above including the link

	// $message = wordwrap($message, 70);
	$headers = 'From:noreply@zeniea.com' . "\r\n"; // Set from headers
	$mail = mail($to, $subject, $message, $headers); // Send our email
	if($mail){
	  echo "";
	}else{
	  echo "Mail sending failed.";
	}

}

?>
