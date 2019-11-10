<?php
namespace zen3mp;
use \DateTime;

class Notification {
	private $user_obj;
	private $spdo;

	public function __construct($user, $spdo){
		$this->user_obj = new User($user, $spdo);
		$this->spdo = $spdo;
		$this->utils = new Utils();
	}

  public function getUnreadNumber() {
    	$userLoggedIn = $this->user_obj->getUsername();
		$stmt = $this->spdo->prepare('SELECT COUNT(*) FROM notifications WHERE viewed = ? AND user_to = ?');
		$stmt->execute(["no", $userLoggedIn]);
		$num_unread = $stmt->fetchColumn();
		//$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed='no' AND user_to='$userLoggedIn'");
		//return mysqli_num_rows($query);
		return $num_unread;
  }

  public function getNotifications($data, $limit) {

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
		//$set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed='yes' WHERE user_to='$userLoggedIn'");

		$stmt = $this->spdo->prepare('SELECT * FROM notifications WHERE user_to = ? ORDER BY id DESC');
		$stmt->execute([$userLoggedIn]);

		$stmt2 = $this->spdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_to = ? ORDER BY id DESC');
		$stmt2->execute([$userLoggedIn]);
		$num_rows = $stmt->fetchColumn();
		//$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to='$userLoggedIn' ORDER BY id DESC");

	    if($num_rows == 0) {
	      echo "You have no notifications.";
	      return;
	    }

		$num_iterations = 0; // num messages checked
		$count = 1; // number of messages posted

		while($row = $stmt->fetch()) {

			if($num_iterations++ < $start)
				continue;

			if($count > $limit) {
				break;
			} else {
				$count++;
			}

    		$user_from = $row['user_from'];
			$date_time = $row['datetime'];

			$user_data_query = $this->spdo->prepare('SELECT * FROM users WHERE username = ?');
			$user_data_query->execute([$user_from]);
			$user_data = $user_data_query->fetch();
	        // $user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$user_from'");
	        // $user_data = mysqli_fetch_array($user_data_query);

	        //Timeframe
	        $time_message = $this->datetime($date_time);


    		$opened = $row['opened'];
			$style = ($row['opened'] == 'no') ? "background-color: #374925;" : "";

			$return_string .= " <a href='" . $row['link'] . "' class='list-group-item list-group-item-action flex-column align-items-start'>
													<div class='user_found_messages' style='" . $style . "'>
                        <div class='d-flex justify-content-between'>
                        <span class='mb-1'>
													      <img src='" . $user_data['avatar'] . "' class='list-group-avatar'>
                              </span></div>
                        <p class='timestamp_smaller' id='grey'>" . $time_message . "</p>" . $row['message'] . "
                        </a></div>";
		}

		//if posts were loaded
		if($count > $limit) {
			$return_string .= "<input type='hidden' class='nextPageDropDownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropDownData' value='false'>";
		} else {
			$return_string .= "<input type='hidden' class='noMoreDropDownData' value='true'> <p style='text-align: center; margin: 3px;'>No more notifications to load.</p>";
		}

		return $return_string;

  }

  public function insertNotification($post_id, $user_to, $type) {
    $userLoggedIn = $this->user_obj->getUsername();
    $userLoggedInDisplayName = $this->user_obj->getDisplayName();

    $date_time = date("Y-m-d H:i:s");

    switch($type) {
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

    $link = "post.php?id=" . $post_id;
    // $insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES(0, '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
	$stmt = $this->spdo->prepare('INSERT INTO notifications VALUES(0, ?, ?, ?, ?, ?, ?, ?)');
	$stmt->execute([$user_to, $userLoggedIn, $message, $link, $date_time, "no", "no"]);
  }

	private function datetime($date_time) {

		//post timeframe
		$date_time_now = date("Y-m-d H:i:s");
		$start_date = new DateTime($date_time); // Time of post
		$end_date = new DateTime($date_time_now); // current time

		$interval = $start_date->diff($end_date);
		if($interval->y >= 1) {
			if($interval == 1)
				$time_message = $interval->y . " year ago"; // 1 year ago
			else
				$time_message = $interval->y . " years ago"; // 1+ year ago
		}
		else if ($interval->m >= 1) {
			if($interval->d == 0) {
				$days = " ago";
			}
			else if($interval->d == 1) {
				$days = $interval->d . " day ago";
			}
			else {
				$days = $interval->d . " days ago";
			}

			if($interval->m == 1){
				$time_message = $interval->m . " month " . $days;
			} else {
				$time_message = $interval->m . " months " . $days;
			}
		}
		else if($interval->d >= 1) {
			if($interval->d == 1) {
				$time_message = "Yesterday";
			}
			else {
				$time_message = $interval->d . " days ago";
			}
		}
		else if($interval->h >= 1) {
			if($interval->h == 1) {
				$time_message = $interval->h . " hour ago";
			}
			else {
				$time_message = $interval->h . " hours ago";
			}
		}
		else if($interval->i >= 1) {
			if($interval->i == 1) {
				$time_message = $interval->i . " minute ago";
			}
			else {
				$time_message = $interval->i . " minutes ago";
			}
		}
		else {
			if($interval->s < 30) {
				$time_message = "Just now";
			}
			else {
				$time_message = $interval->s . " seconds ago";
			}
		}
		return $time_message;
	}

}


?>
