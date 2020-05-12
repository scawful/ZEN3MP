<?php 
namespace zen3mp;

if (isset($_POST['post']))
{
    $uploadOk = 1;
    $fileToUpload = $_FILES['fileToUpload']['name'];
    //$newFileType = mime_content_type($_FILES['fileToUpload']['name']);
    $targetDirectory = "img/uploads/posts";
    $errorMessage = "";

    // check if file is being uploaded by the user
    if ($fileToUpload != "")
    {
        $newFileName = $targetDirectory . uniqid() . basename($fileToUpload);
        $newFileType = pathinfo($fileToUpload, PATHINFO_EXTENSION);

        if ($_FILES['fileToUpload']['size'] > 10000000) 
        {
			$errorMessage = "Sorry, your file is too large";
			$uploadOk = 0;
		}

        if (strtolower($newFileType) != "jpeg" && strtolower($newFileType) != "png" && strtolower($newFileType) != "jpg" && strtolower($newFileType) != "mp4" && strtolower($newFileType) != "avi" && strtolower($newFileType) != "mkv") 
        {
			$errorMessage = "Invalid filetype, only jpeg, jpg, png, mp4, avi, and mkv are allowed.";
			$uploadOk = 0;
		}

        if($uploadOk) 
        {
            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $newFileName)) 
            {
				//image uploaded okay
			}
            else 
            {
                //image did not upload
                $errorMessage .= "Upload confirmation error.";
				$uploadOk = 0;
			}
		}
    }
    else {
        $newFileName = "";
        $newFileType = "";
    }

    // submit post to database 
    if ($uploadOk) 
    {
        $post = new Post($userLoggedIn, $spdo, $rpdo);
        $newPostBody = (!isset($_POST['post_text']) || empty($_POST['post_text'])) ? "" : $_POST['post_text'];

        if($newPostBody != "") 
        {
          $post->submitPost($newPostBody, 'none', $newFileName, $newFileType);
          header("Location: index.php");
        }
        else 
        {
          $post->submitMediaPost('none', $newFileName, $newFileType);
          header("Location: index.php");
        }

    } 
    else 
    {
        echo "<div style='text-align: center; padding: 2px;' class='alert alert-danger'>
                $errorMessage
              </div>";
    }

}


?>