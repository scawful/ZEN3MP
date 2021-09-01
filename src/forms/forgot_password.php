<?php
namespace zen3mp;

$password_message = "";

if (isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['key']) && !empty($_GET['key'])) 
{
    echo $twig->render('social/forgot_pw.twig');

    if ( isset($_POST['update_password']) ) 
    {
        $new_password = strip_tags($_POST['newpassword']);
        $new_password2 = strip_tags($_POST['newpassword2']);

        $email = $_GET['email'];
        $username_by_email_query = $spdo->prepare('SELECT username FROM users WHERE email = ?');
        $username_by_email_query->execute([$email]);
        $row = $username_by_email_query->fetch();
        $username = $row['username'];

        $password_query = $spdo->prepare('SELECT password FROM users WHERE username = ?');
        $password_query->execute([$username]);
        $row = $password_query->fetch();
        $db_password = $row['password'];

        if ($new_password == $new_password2) 
        {
            if(strlen($new_password) <= 4) 
            {
                $password_message = "Sorry, your password must be greater than 4 characters<br>";
            }
            else 
            {
                $new_password_hash = password_hash($new_password,PASSWORD_DEFAULT);
                $password_query = $spdo->prepare('UPDATE users SET password = ? WHERE username = ?');
                $password_query->execute([$new_password_hash, $username]);
                $password_message = "Password has been changed!<br>";
            }
        } 
        else 
        {
            $password_message = "Passwords do not match<br>";
        }

    }
    else 
    {
        $password_message = "";
    }
} 
else 
{
    echo $twig->render('social/forgot_pw_input.twig');

    if ( isset($_POST['forgot_password']) ) 
    {
        $email = strip_tags($_POST['email']);
        $username = strip_tags($_POST['username']);

        $retrieved_email_query = $spdo->prepare('SELECT email FROM users WHERE username = ?');
        $retrieved_email_query->execute([$email]);
        $row = $retrieved_email_query->fetch();
        $email = $row['email'];

        $verify_hash = md5(rand(0,1000));

        $to      = $em; // Send email to our user
        $subject = 'Zeniea | Password Recovery'; // Give the email a subject
        $message = '
        
        If you did not request to have your password reset, disregard this email.

        Please click this link to reset your password:
        https://www.zeniea.com/forgot.php?email='.$em.'&key='.$verify_hash.'


        '; // message above including the link

        $headers = 'From:noreply@zeniea.com' . "\r\n"; // Set from headers
        $mail = mail($to, $subject, $message, $headers); // Send our email
        if ( $mail ) 
        {
            // email sent successfully
        }
    }
}

?>
