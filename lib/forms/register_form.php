<?php
//Declaring variables to prevent errors
$uname = ""; //Username
$em = ""; //email
$em2 = ""; //email 2
$password = ""; //password
$password2 = ""; //password 2
$date = ""; //Sign up date
$error_array = array(); //Holds error messages

if(isset($_POST['register_button']) && $_POST['g-recaptcha-response']!=""){

	//Registration form values

	//First name
	$uname = strip_tags($_POST['reg_uname']); //Remove html tags
	$uname = str_replace(' ', '', $uname); //remove spaces
	$_SESSION['reg_uname'] = $uname; //Stores first name into session variable

	//email
	$em = strip_tags($_POST['reg_email']); //Remove html tags
	$em = str_replace(' ', '', $em); //remove spaces
	$_SESSION['reg_email'] = $em; //Stores email into session variable

	//email 2
	$em2 = strip_tags($_POST['reg_email2']); //Remove html tags
	$em2 = str_replace(' ', '', $em2); //remove spaces
	$_SESSION['reg_email2'] = $em2; //Stores email2 into session variable

	//Password
	$password = strip_tags($_POST['reg_password']); //Remove html tags
	$password2 = strip_tags($_POST['reg_password2']); //Remove html tags

	$date = date("Y-m-d"); //Current date

	//recaptcha
	$secret = '6LdNwpoUAAAAAD_FHh8KyaVgYDNLY9V9KM_dMsU6';
	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
	$responseData = json_decode($verifyResponse);
	if($responseData->success) {

		if($em == $em2) {
			//Check if email is in valid format
			if(filter_var($em, FILTER_VALIDATE_EMAIL)) {

				$em = filter_var($em, FILTER_VALIDATE_EMAIL);

				//Check if email already exists
				$e_check = mysqli_query($connect_social, "SELECT email FROM users WHERE email='$em'");

				//Count the number of rows returned
				$num_rows = mysqli_num_rows($e_check);

				if($num_rows > 0) {
					array_push($error_array, "Email already in use<br>");
				}

			}
			else {
				array_push($error_array, "Invalid email format<br>");
			}

		}
		else {
			array_push($error_array, "Emails don't match<br>");
		}


		if(strlen($uname) > 25 || strlen($uname) < 2) {
			array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
		}

		if($password != $password2) {
			array_push($error_array,  "Your passwords do not match<br>");
		}
		else {
			if(preg_match('/[^A-Za-z0-9!@#$%]/', $password)) {
				array_push($error_array, "Your password can only contain english characters, numbers or !@#$% <br>");
			}
		}

		if(strlen($password > 30 || strlen($password) < 5)) {
			array_push($error_array, "Your password must be betwen 5 and 30 characters<br>");
		}
	} else {
		array_push($error_array, "Captcha Failed!!!");
}


		if(empty($error_array)) {
				$password = password_hash($password,PASSWORD_DEFAULT);

				//Generate username by concatenating first name and last name
				$displayname = $uname;
				$check_username_query = mysqli_query($connect_social, "SELECT username FROM users WHERE username='$displayname'");

				$i = 0;
				//if username exists add number to username
				while(mysqli_num_rows($check_username_query) != 0) {
						$i++; //Add 1 to i
						$displayname = $displayname . "_" . $i;
						$check_username_query = mysqli_query($connect_social, "SELECT username FROM users WHERE username='$displayname'");
				}

				//Profile picture assignment
				$rand = rand(1, 5); //Random number between 1 and

				if($rand == 1)
					$profile_pic = "/lib/assets/images/profile_pics/defaults/head_deep_blue.png";
				else if($rand == 2)
					$profile_pic = "/lib/assets/images/profile_pics/defaults/head_emerald.png";
				else if($rand == 3)
					$profile_pic = "/lib/assets/images/profile_pics/defaults/head_red.png";
				else if($rand == 4)
					$profile_pic = "/lib/assets/images/profile_pics/defaults/head_alizarin.png";
				else if($rand == 5)
					$profile_pic = "/lib/assets/images/profile_pics/defaults/head_pomegranate.png";

				$verify_hash = md5(rand(0,1000));

				// Verify User Email

				$to      = $em; // Send email to our user
				$subject = 'Zeniea | Registration Verification and Class Creation'; // Give the email a subject
				$message = '
				Welcome to Zeniea!

				Thanks for signing up, ' . $uname . '!
				Your account has been created, however before you can log in you must create your character and decide their class. This acts as the verification process to prevent spammers.

				ZEN3MP is an experimental project so this verification page may change. Eventually there will be an update character page for any legacy accounts caught up in development changes.

				Please click this link to activate your account and set up your character:
				http://www.zeniea.com/?verify&email='.$em.'&key='.$verify_hash.'


				'; // message above including the link

				$headers = 'From:noreply@zeniea.com' . "\r\n"; // Set from headers
				$mail = mail($to, $subject, $message, $headers); // Send our email
				if($mail){
					$query = mysqli_query($connect_social, "INSERT INTO users VALUES (0, '$uname', '$displayname', '$em', '$password', '$date', '$profile_pic', '0', '0', 'yes', ',', '/img/default-wallpaper.png', 'New User', 'no', '$verify_hash')");

					$new_user_id = mysqli_insert_id($connect_social);

					$about_query = mysqli_query($connect_social, "INSERT INTO users_about (id, user_id, style) VALUES (0, '$new_user_id', 'purpleStyle')");

					array_push($error_array, "<span style='color: #14C800;'>You're all set! Wait for the email to complete the registration process. </span><br>");
				}else{
					echo "Mail sending failed.";
				}

				//Clear session variables
				$_SESSION['reg_uname'] = "";
				$_SESSION['reg_email'] = "";
				$_SESSION['reg_email2'] = "";
		}

}
?>
