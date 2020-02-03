<?php 
namespace zen3mp;

class ImageUpload {
    public $newImageName;
    public $imageFileName;
    public $errorMessage;
    public $aboveDirectory;

    public function __construct() {

    }

    public function uploadImage($imageName, $directory) 
    {
        $uploadOk = 1;
        $imageName = $_FILES['new-image']['name'];
        $this->errorMessage = "";
      
        if($imageName != "")
        {
            $targetDir = $directory;
            $this->imageFileName =  uniqid() . basename($imageName);
            $this->aboveDirectory = $targetDir;
            $imageName = $targetDir . $this->imageFileName;
            $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);
      
            if($_FILES['new-image']['size'] > 10000000)
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
                if(move_uploaded_file($_FILES['new-image']['tmp_name'], $imageName)) {
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

    public function getAboveImageDir() 
    {
        $this->newImageName = str_replace("../../", "/", $this->imageFileName);
        return $this->newImageName;
    }

    public function parseDirectory($directory)
    {
        $this->aboveDirectory = str_replace("../../", "", $directory);
        return $this->aboveDirectory;
    }

    public function getNewImageDir() 
    {
        $this->newImageName = str_replace("../", "https://zeniea.com/", $imageName);
        return $this->newImageName;
    }

    public function getNewImageFile() 
    {
        return $this->imageFileName;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }


}
?>