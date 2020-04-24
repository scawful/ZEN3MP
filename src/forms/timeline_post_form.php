<?php
namespace zen3mp;
use zen3mp\Post as Post;
// Main Timeline Post
if(isset($_POST['post']))
{

    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $videoName = $_FILES['videoToUpload']['name'];
    $directory = "img/uploads/posts";
    $errorMessage = "";

	if($imageName != ""  && $_FILES['fileToUpload']['size'] != 0) {
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

    if ($videoName != "")
    {
        $upload = new Upload();
        $uploadOk = $upload->uploadVideo($videoName, $directory);
        if ($uploadOk)
        {
            $post = new Post($userLoggedIn, $spdo, $rpdo);
            $newPostBody = (!isset($_POST['post_text']) || empty($_POST['post_text'])) ? "" : $_POST['post_text'];
            if ($newPostBody != "")
            {
                $post->submitPost($newPostBody, 'none', NULL, $videoName);
                header("Location: index.php");
            }
            else {
                //$post->submitVideo('none', NULL, $videoName);
                header("Location: index.php");
            }
        } else {
            // error message for video 
            echo $upload->getErrorMessage();
        }
    }

    if ($uploadOk) {
        $post = new Post($userLoggedIn, $spdo, $rpdo);
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
