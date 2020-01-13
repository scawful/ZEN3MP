<?php
namespace zen3mp;
if(isset($_POST['new_topic'])) 
{
    $title = $_POST['title'];
    $body = $_POST['body'];
    $category = $_POST['category'];
    $reply_to = "";
    $sticky = 0;
    $board_obj->postNewTopic($title, $body, $category, $sticky, $reply_to);
}

if(isset($_POST['reply_topic']))
{
    $reply_to = $_POST['reply_to'];
    $body = $_POST['reply_body'];
    $board_obj->postReply($body, $reply_to);
}

?>