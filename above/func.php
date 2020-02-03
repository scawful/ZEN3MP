<?php
namespace zen3mp;

if ($isLoggedIn == True)
{
    $admin_auth_query = $spdo->prepare('SELECT user_title FROM users WHERE username = ?');
    $admin_auth_query->execute([$userLoggedIn]);
    $admin_auth_code = $admin_auth_query->fetch();
    $admin = "Admin";
    if($admin_auth_code['user_title'] != $admin) {
        header("Location: https://zeniea.com/");
    }
} else {
    header("Location: https://zeniea.com");
}

if (isset($_POST['news_post']))
{
    $post = new Post($userLoggedIn, $spdo, $rpdo);
    $newsPostBody = (!isset($_POST['post_text']) || empty($_POST['post_text'])) ? "" : $_POST['post_text'];
    $newsPostTitle = (!isset($_POST['post_title']) || empty($_POST['post_title'])) ? "" : $_POST['post_title'];
    $added_by = $_POST['added_by'];
    $category = $_POST['category'];
    if($newsPostBody != "") {
        $post->submitNewsPost($newsPostTitle, $newsPostBody, $added_by, $category);
    }
    else {
        echo "<div style='text-align: center; padding: 2px;' class='alert alert-danger'>
                Error: News post was empty!
            </div>";
    } 
}

if (isset($_POST['new_quest']))
{
    $title = $_POST['title'];
    $description = $_POST['contents'];
    $category = $_POST['category'];
    $lvreq = 1;
    $quest_obj->addQuestCategory($title, $description, $lvreq, $category);
}

if (isset($_POST['new_quest_page']))
{
    $ImageUpload = new ImageUpload();
    $uploadOk = 1;
    $title = $_POST['title'];
    $body = $_POST['contents'];
    $quest_id = $_POST['quest-category'];
    $authors_note = $_POST['authors-note'];

    $imageName = $_FILES['new-image']['name'];
    $directory = "../img/quests/pages/";

    $uploadOk = $ImageUpload->uploadImage($imageName, $directory);
    if($uploadOk) 
    {
        $media = $ImageUpload->getNewImageFile();
        $quest_obj->addQuestPage($title, $body, $media, $authors_note, $quest_id);
    } else {
        echo $ImageUpload->getErrorMessage();
    }
}

if (isset($_POST['edit_page']))
{
    $ImageUpload = new ImageUpload();
    $uploadOk = 1;
    $title = $_POST['title'];
    $body = $_POST['contents'];
    $quest_id = $_POST['quest-category'];
    $authors_note = $_POST['authors-note'];

    $imageName = $_FILES['new-image']['name'];
    $directory = "../img/quests/pages/";

    $uploadOk = $ImageUpload->uploadImage($imageName, $directory);
    if($uploadOk) 
    {
        $media = $ImageUpload->getNewImageFile();
        $quest_obj->addQuestPage($title, $body, $media, $authors_note, $quest_id);
    } else {
        echo $ImageUpload->getErrorMessage();
    }
}

if (isset($_POST['delete_quest_page']))
{
    $page = $_POST['quest-page'];
    $quest_obj->deleteQuestPage($page);
}

if (isset($_POST['twitter_post']))
{
    $post = $_POST['timeline_post'];
    // $post = "'" . $post . "'";
    $command = escapeshellcmd('/www/zeniea.com/public/src/scripts/twitter_post.py -p' . $post);
    $output = exec($command);
    echo $output;
}

if(isset($_POST['new_item']))
{
    $item = new Inventory($connect_social, $connect_rpg, $userLoggedIn);
    $item_name = $_POST['item-name'];
    $item_type = $_POST['item-type'];
    $item_desc = $_POST['item-desc'];
    $item_price = $_POST['item-price'];
    $req_level = $_POST['req-level'];
    $equip_zone = $_POST['equip-zone'];
    $str = $_POST['strength'];
    $int = $_POST['intelligence'];
    $wpr = $_POST['willpower'];
    $agt = $_POST['agility'];
    $spd = $_POST['speed'];
    $end = $_POST['endurance'];
    $per = $_POST['personality'];
    $wsd = $_POST['wisdom'];
    $lck = $_POST['luck'];

    $uploadOk = 1;
    $imageName = $_FILES['item-icon']['name'];
    $errorMessage = "";

    if($imageName != "")
    {
        $targetDir = "/img/uploads/items/";
        $imageName = $targetDir . uniqid() . basename($imageName);
        $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

        if($_FILES['item-icon']['size'] > 10000000)
        {
            $errorMessage = "Sorry your file is too large";
            $uploadOk = 0;
        }

        if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") 
        {
            $errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
            $uploadOk = 0;
        }

        if($uploadOk) 
        {
            if(move_uploaded_file($_FILES['item-icon']['tmp_name'], $imageName)) {
                //image uploaded okay
            }
            else {
                //image did not upload
                $uploadOk = 0;
            }
        }

    }

    if($uploadOk) {
        $item->addNewItem($item_name, $item_type, $item_desc, $item_price, $imageName, $equip_zone, $req_level, $str, $int, $wpr, $agt, $spd, $end, $per, $wsd, $lck, $userLoggedIn);
    }
    else {
        echo "<div style='text-align: center; padding: 2px;' class='alert alert-danger'>
                $errorMessage
                </div>";
    }


}

if(isset($_POST['new_image']))
{
    $ImageUpload = new ImageUpload();
    $directory = $_POST['directory'];
    $imageName = $_FILES['new-image']['name'];
    $uploadOk = $ImageUpload->uploadImage($imageName, $directory);
    if($uploadOk) 
    {
        echo "<div style='text-align: center;' class='alert alert-primary'>
                <a href='https://zeniea.com/" . $ImageUpload->parseDirectory($directory) . "/" . $ImageUpload->getNewImageFile() . "'> " . $ImageUpload->getNewImageFile() . "</a>
              </div>";
    } else {
        echo $ImageUpload->getErrorMessage();
    }
}

?>
