<?php 
namespace zen3mp;

class ImageUpload {
    public $newImageName;
    public $imageFileName;
    public $errorMessage;

    public function __construct() {

    }

    public function uploadImage($imageName, $directory) 
    {
        $uploadOk = 1;
        $imageName = $_FILES['new-image']['name'];
        $this->errorMessage = "";
      
        if($imageName != "")
        {
            $targetDir = "../../img/" . $directory;
            $this->imageFileName =  uniqid() . basename($imageName);
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
                    $this->newImageName = str_replace("../../", "/", $imageName);
                }
                else {
                    //image did not upload
                    $uploadOk = 0;
                }
            }
      
        }
        return $uploadOk;
    }

    public function getNewImageDir() 
    {
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