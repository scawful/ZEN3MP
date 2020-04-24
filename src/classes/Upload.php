<?php
namespace zen3mp;

class Upload 
{
    public $errorMessage;
    public $fileName;
    public $filePath;

    public function uploadImage($files_obj, $directory)
    {
        $uploadOk = 1;
        $imageName = $_FILES['imageToUpload']['name'];
        $this->errorMessage = "";

        if ($imageName != "")
        {
            $this->fileName =  uniqid() . basename($imageName);
            $imageName = $directory . $this->fileName;
            $this->filePath = $imageName;
            $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);
      
            if($_FILES['imageToUpload']['size'] > 10000000)
            {
                $this->errorMessage = "Sorry your file is too large";
                $uploadOk = 0;
            }
      
            if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
                $this->errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
                $uploadOk = 0;
            }
      
            if($uploadOk) 
            {
                if (move_uploaded_file($_FILES['imageToUpload']['tmp_name'], $imageName)) {
                    //image uploaded okay
                }
                else {
                    //image did not upload
                    $uploadOk = 0;
                }
            }
        }
        return $uploadOk;
    }

    public function uploadVideo($files_obj, $directory)
    {
        $uploadOk = 1;
        $videoName = $_FILES['videoToUpload']['name'];
        $this->errorMessage = "";
        
        if ($videoName != "")
        {
            $this->fileName =  uniqid() . basename($videoname);
            $videoName = $directory . $this->fileName;
            $this->filePath = $videoName;
            $videoFileType = pathinfo($videoName, PATHINFO_EXTENSION);
      
            if($_FILES['videoToUpload']['size'] > 10000000)
            {
                $this->errorMessage = "Sorry your file is too large";
                $uploadOk = 0;
            }
      
            if (strtolower($videoFileType) != "mp4" && strtolower($videoFileType) != "avi" && strtolower($videoFileType) != "mkv") 
            {
                $this->errorMessage = "Sorry, only mp4, avi and mkv files are allowed";
                $uploadOk = 0;
            }
      
            if($uploadOk) 
            {
                if (move_uploaded_file($_FILES['videoToUpload']['tmp_name'], $videoName)) {
                    //image uploaded okay
                }
                else {
                    //image did not upload
                    $uploadOk = 0;
                }
            }
        }
        return $uploadOk;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

}

?>