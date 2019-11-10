<?php

if(isset($_POST['login_button'])) {

	$isAuthenticated = false;

	$email = filter_var($_POST['login_email'], FILTER_SANITIZE_EMAIL); //sanitize email

	$_SESSION['login_email'] = $email; //Store email into session variable
	$password = $_POST['login_password']; //Get password

	$check_db_query = $spdo->prepare('SELECT * FROM users WHERE email = ? ');
	$check_db_query->execute([$email]);

	if($row = $check_db_query->fetch()) {

			if($row['verify_user'] == 'yes') {
				if (password_verify($password, $row['password'])) {
					$username = $row['username'];
					// Check if closed, reopen if closed
					$user_closed_query = $spdo->prepare('SELECT * FROM users WHERE email = ? AND user_closed = ?');
					$user_closed_query->execute([$email, 'yes']);
					if($user_closed_query->rowCount() == 1) {
						$reopen_account = $spdo->prepare('UPDATE users SET user_closed = ? WHERE email = ?');
						$reopen_account->execute(['no', $email]);
					}
					$isAuthenticated = true;
				} else {
					array_push($error_array, "IncorrectCredentials");
				}
			} else {
				array_push($error_array, "UserNotVerified");
			}

			if($isAuthenticated) {
				$_SESSION['username'] = $username;

				// Set Auth Cookies if 'Remember Me' checked
		        if (! empty($_POST["remember"])) {
		            setcookie("user_login", $username, $cookie_expiration_time);

		            $random_password = $utils->getToken(16);
		            setcookie("random_password", $random_password, $cookie_expiration_time);

		            $random_selector = $utils->getToken(32);
		            setcookie("random_selector", $random_selector, $cookie_expiration_time);

		            $random_password_hash = password_hash($random_password, PASSWORD_DEFAULT);
		            $random_selector_hash = password_hash($random_selector, PASSWORD_DEFAULT);

		            $expiry_date = date("Y-m-d H:i:s", $cookie_expiration_time);

		            // mark existing token as expired
		            $userToken = $user_obj->getTokenByUser($username, 0);
		            if (! empty($userToken["id"])) {
		                $user_obj->markExpired($userToken["id"]);
		            }
		            // Insert new token
		            $user_obj->insertToken($username, $random_password_hash, $random_selector_hash, $expiry_date);
		        } else {
		            $utils->clearAuthCookie();
		        }

				header("Location: index.php");
				exit();
			}


		} else {
			array_push($error_array, "UserNotExist");
			// echo "This user does not exist"; //email entered does not match any in DB
	}

}

if(isset($_POST['resend'])) {

	$email = filter_var($_POST['login_email'], FILTER_SANITIZE_EMAIL); //sanitize email

	$_SESSION['login_email'] = $email; //Store email into session variable

	$check_database_query1 = $spdo->prepare('SELECT * FROM users WHERE email = ?');
	$check_database_query1->execute([$email]);

	$row = $check_database_query1->rowCount();
	$uname = $row['username'];

	$verify_hash = md5(rand(0,1000));

	$query = $spdo->prepare('UPDATE users SET verify_hash = ? WHERE email = ?');
	$query->execute([$verify_hash, $email]);

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
	} else {
	  echo "Mail sending failed.";
	}

}

?>
