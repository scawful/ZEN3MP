<?php
namespace zen3mp;

if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['key']) && !empty($_GET['key'])) 
{
    $verify_user_email = $_GET['email'];
    $verify_user_hash = $_GET['key'];

    $verify_query = $spdo->prepare('SELECT * FROM users WHERE email = ? AND verify_hash = ? AND verify_user = ?');
    $verify_query->execute([$verify_user_email, $verify_user_hash, 'no']);
    $row = $verify_query->fetch();
    $user_id = $row['id'];
    $username = $row['username'];

    if($verify_user_hash == $row['verify_hash'])
        echo '';
    else
        header("Location: index.php");

    if(isset($_POST['verify'])) 
    {
        echo $_POST['hidden'];
        $new_verify_hash = md5(rand(0,1000));
        $update_user_query = mysqli_query($connect_social, "UPDATE users SET verify_hash='$new_verify_hash', verify_user='yes', user_closed='no' WHERE username='$username'");
        $_SESSION['username'] = $username;
        header("Location: https://zeniea.com/".$username."");
        exit();
    }

} 
else 
{
  header("Location: index.php");
}

?>
