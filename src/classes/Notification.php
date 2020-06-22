<?php
namespace zen3mp;
use \DateTime;

class Notification 
{
    private $user_obj;
    private $char_obj;
    private $spdo;
    private $rpdo;

    public function __construct($user, $character, $spdo, $rpdo)
    {
        $this->user_obj = new User($user, $spdo);
        $this->username = $this->user_obj->getUsername();
        $this->utils = new Utils();
        $this->char_obj = $character;
        $this->spdo = $spdo;
        $this->rpdo = $rpdo;
	}

    public function getUnreadNumber() 
    {
		$userLoggedIn = $this->user_obj->getUsername();
		$stmt = $this->spdo->prepare('SELECT COUNT(*) FROM notifications WHERE viewed = ? AND user_to = ?');
		$stmt->execute(["no", $userLoggedIn]);
		$num_unread = $stmt->fetchColumn();
		return $num_unread;
    }
    
    public function getNotificationUserTo($id)
    {
        $stmt = $this->spdo->prepare('SELECT user_to FROM notifications WHERE id = ?');
        $stmt->execute([$id]);
        $user_to = $stmt->fetchColumn();
        return $user_to;
    }

    public function getNotificationFrom($id)
    {
        $stmt = $this->spdo->prepare('SELECT user_from FROM notifications WHERE id = ?');
        $stmt->execute([$id]);
        $user_from = $stmt->fetchColumn();
        return $user_from;
    }

    public function getNotificationMessage($id)
    {
        $stmt = $this->spdo->prepare('SELECT message FROM notifications WHERE id = ?');
        $stmt->execute([$id]);
        $msg = $stmt->fetchColumn();
        return $msg;
    }

    public function getNotificationDate($id)
    {
        $stmt = $this->spdo->prepare('SELECT datetime FROM notifications WHERE id = ?');
        $stmt->execute([$id]);
        $datetime = $stmt->fetchColumn();
        $new_date = date_create($datetime);
        $format_datetime = date_format($new_date, "m/d/Y H:i");
        return $format_datetime;
    }

    public function getNotificationType($id)
    {
        $stmt = $this->spdo->prepare('SELECT type FROM notifications WHERE id = ?');
        $stmt->execute([$id]);
        $type = $stmt->fetchColumn();
        return $type;
    }

    public function getNotificationTypeID($id)
    {
        $stmt = $this->spdo->prepare('SELECT type_id FROM notifications WHERE id = ?');
        $stmt->execute([$id]);
        $type_id = $stmt->fetchColumn();
        return $type_id;
    }

    public function getNextInsertID()
    {
        $stmt = $this->spdo->prepare('SELECT MAX(id) FROM notifications');
        $stmt->execute();
        $id = $stmt->fetchColumn() + 1;
        return $id;
    }

    public function getNotifications($data, $limit) 
    {
		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";

		if($page == 1) {
			$start = 0;
		} else {
			$start = ($page - 1) * $limit;
		}

		$set_viewed = $this->spdo->prepare('UPDATE notifications SET viewed = ? WHERE user_to = ?');
		$set_viewed->execute(["yes", $userLoggedIn]);

		$stmt = $this->spdo->prepare('SELECT * FROM notifications WHERE user_to = ? ORDER BY id DESC');
		$stmt->execute([$userLoggedIn]);

		$stmt2 = $this->spdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_to = ? ORDER BY id DESC');
		$stmt2->execute([$userLoggedIn]);
		$num_rows = $stmt2->fetchColumn();

	    if ($num_rows == 0) {
	      echo "You have no notifications.";
	      return;
	    }

		$num_iterations = 0; // num messages checked
		$count = 1; // number of messages posted

		while ($row = $stmt->fetch()) {

			if($num_iterations++ < $start)
				continue;

			if($count > $limit) {
				break;
			} else {
				$count++;
			}

			$user_from = $row['user_from'];
            $date_time = $row['datetime'];
            
            $user_from_obj = new User($user_from, $this->spdo, $this->rpdo);
            $user_from_avatar = $user_from_obj->getAvatar();

	        //Timeframe
	        $time_message = $this->utils->datetime($date_time);

			$opened = $row['opened'];
			$style = ($row['opened'] == 'no') ? "background-color: #374925;" : "";

			$return_string .= " <a href='" . $row['link'] . "' class='list-group-item list-group-item-action flex-column align-items-start'>
								<div class='user_found_messages' style='" . $style . "'>
                                    <div class='d-flex justify-content-between'>
                                        <img src='" . $user_from_avatar . "' class='list-group-avatar float-left'>
                                        <small>" . $time_message . "</small>
                                    </div>
                                    " . $row['message'] . "
                                </div></a>";
		}

		//if posts were loaded
		if($count > $limit) {
			$return_string .= "<input type='hidden' class='nextPageDropDownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropDownData' value='false'>";
		} else {
			$return_string .= "<input type='hidden' class='noMoreDropDownData' value='true'> <p style='text-align: center; margin: 3px;'>No more notifications to load.</p>";
		}

		return $return_string;

    }

    public function setPostNotificationRead($post_id)
    {
        $opened_query = $this->spdo->prepare('UPDATE notifications SET opened = ? WHERE user_to = ? AND link LIKE ? ');
		$opened_query->execute(["yes", $this->username, "%=$post_id"]);
    }

    public function insertNotification($post_id, $user_to, $type) 
    {
		$userLoggedIn = $this->user_obj->getUsername();
		$userLoggedInDisplayName = $this->user_obj->getDisplayName();

		$date_time = date("Y-m-d H:i:s");

        switch ($type) 
        {
		    case 'comment':
		        $message = $userLoggedInDisplayName . " commented on your post";
		        break;
		    case 'like':
		        $message = $userLoggedInDisplayName . " liked your post";
		        break;
		    case 'profile_post':
		        $message = $userLoggedInDisplayName . " posted on your profile";
		        break;
		    case 'profile_comment':
		        $message = $userLoggedInDisplayName . " commented on your profile post";
		        break;
		    case 'comment_non_owner':
		        $message = $userLoggedInDisplayName . " commented on a post you commented on";
                break;
		}

		$link = "/?notification=" . $this->getNextInsertID();
		$stmt = $this->spdo->prepare('INSERT INTO notifications VALUES(0, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$stmt->execute([$user_to, $userLoggedIn, $message, $link, $type, $post_id, $date_time, "no", "no"]);
    }
    
    public function insertRPGNotification($post_id, $user_to, $type, $modifier) 
    {
        $userLoggedIn = $this->user_obj->getUsername();
		$userLoggedInDisplayName = $this->user_obj->getDisplayName();

		$date_time = date("Y-m-d H:i:s");
        $link = "";

        switch ($type) 
        {
		    case 'gold_added':
                $message = $modifier . " gold awarded for this post.";
                $link = "post.php?id=" . $post_id;
                break;
            case 'level_up':
                $message = $this->char_obj->getCharacterName() . " leveled up to level " . $modifier . ".";
                $link = "/?notification=" . $this->getNextInsertID();
                break;
		}

		$stmt = $this->spdo->prepare('INSERT INTO notifications VALUES(0, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$stmt->execute([$user_to, $userLoggedIn, $message, $link, $type, $post_id, $date_time, "no", "no"]);
    }
    
    public function openLevelNotif()
    {
        $userLoggedIn = $this->user_obj->getUsername();
        $opened_query = $this->spdo->prepare('UPDATE notifications SET opened = ? WHERE user_to = ? AND link = ? ');
		$opened_query->execute(["yes", $userLoggedIn, $userLoggedIn]);
    }

}


?>
