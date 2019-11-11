<?php
namespace zen3mp;
use zen3mp\Post as Post;
// Main Timeline Post
if(isset($_POST['post']))
{

    $uploadOk = 1;
	$imageName = $_FILES['fileToUpload']['name'];
	$errorMessage = "";

	if($imageName != "") {
		$targetDir = "img/uploads/posts/";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if($_FILES['fileToUpload']['size'] > 10000000) {
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}

		if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
			$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
			$uploadOk = 0;
		}

		if($uploadOk) {
			if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
				//image uploaded okay
			}
			else {
				//image did not upload
				$uploadOk = 0;
			}
		}

	}

    if($uploadOk) {
        $post = new Post($userLoggedIn, $spdo);
        $newPostBody = (!isset($_POST['post_text']) || empty($_POST['post_text'])) ? "" : $_POST['post_text'];
        if($newPostBody != "") {
          $post->submitPost($newPostBody, 'none', $imageName);
          header("Location: index.php");
        }
        else {
          $post->submitPhoto('none', $imageName);
          header("Location: index.php");
        }
    } else {
        echo "<div style='text-align: center; padding: 2px;' class='alert alert-danger'>
            $errorMessage
            </div>";
    }


}
?>
