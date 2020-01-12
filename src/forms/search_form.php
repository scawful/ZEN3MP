<?php
$prompt_string = "";
$search_string = "";

if(isset($_GET['q'])) {
    $query = $_GET['q'];
}
else {
    $query = "";
}

if(isset($_GET['type'])) {
    $type = $_GET['type'];
}
else {
    $type = "username";
}

if($query == "") {
    $prompt_string = "You must enter something in the search box";
}
else {

if($type == "username") 
{
    $usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
    $usersReturned;
}
else 
{
    $names = explode(" ", $query);
    if(count($names) == 3)
        $usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE (username LIKE '%$names[2]%') AND user_closed='no'");
    else if(count($names) == 2)
        $usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE (username LIKE '%$names[1]%') AND user_closed='no'");
    else
        $usersReturned = mysqli_query($connect_social, "SELECT * FROM users WHERE (username LIKE '%$names[0]%') AND user_closed='no'");

}

if(mysqli_num_rows($usersReturned) == 0) 
{
    $prompt_string = "Can't find anyone with a " . $type . " like: " . $query;
}
else 
{
    $prompt_string = mysqli_num_rows($usersReturned) . " results found: ";
}
$prompt_string .= "<p id='grey'>Try searching for: ";
$prompt_string .= "<a href='search.php?q=" . $query . "&type=name'>Users</a>, <a href='search.php?q=" . $query . "&type=post'>Posts</a></p><hr>";

while($row = mysqli_fetch_array($usersReturned)) {
    //$user_obj = new User($user_obj->getUsername(), $spdo);

    $button = "";
    $mutual_friends = "";

    if($user_obj->getUsername() != $row['username']) {

        //Generate button depending on friendship status
        if($user_obj->isFriend($row['username']))
            $button = "<input type='submit' name='" . $row['username'] . "' class='btn btn-danger' value='Remove Friend'>";
        else if($user_obj->didReceiveRequest($row['username']))
            $button = "<input type='submit' name='" . $row['username'] . "' class='btn btn-warning' value='Respond to Request'>";
        else if($user_obj->didSendRequest($row['username']))
            $button = "<input name'" . $row['username'] . "' class='btn btn-default' value='Request Sent'>";
        else
            $button = "<input type='submit' name='" . $row['username'] . "' class='btn btn-success' value='Add Friend'>";

        $mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";

        //button forms

        if(isset($_POST[$row['username']])) {

        if($user_obj->isFriend($row['username'])) {
            $user_obj->removeFriend($row['username']);
            header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        }
        else if ($user_obj->didReceiveRequest($row['username'])) {
            header("Location: requests.php");
        }
        else if($user_obj->didSendRequest($row['username'])) {
            // optional functionality
        }
        else {
            $user_obj->sendRequest($row['username']);
            header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        }

        }

    }

    $search_string .= "<div class='search_result'>
            <div class='searchPageFriendButtons'>
                <form action='' method='POST'>
                " . $button . "
                </form>
            </div>

            <div class='result_profile_pic'>
            <a href='" . $row['username'] . "'><img src='" . $row['avatar'] . "' style='height: 100px;'></a>
            </div>

            <a href='" . $row['username'] . "'>" . $row['displayname'] . "</a>
            <p id='grey'> " . $row['username'] . "</p><br> " . $mutual_friends . "<br>
            <hr>
            </div>";

} // end while

}

?>