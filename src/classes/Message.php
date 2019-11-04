<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("Utils.php");
class Message extends Utils {
	private $user_obj;
	private $connect_social;
	private $spdo;

	public function __construct($connect_social, $user, $spdo){
		$this->con = $connect_social;
		$this->user_obj = new User($user, $spdo);
		$this->spdo = $spdo;
	}

  public function getMostRecentUser() {
    $userLoggedIn = $this->user_obj->getUsername();

    //$query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER by id DESC LIMIT 1");

		$stmt = $this->spdo->prepare('SELECT user_to, user_from FROM messages WHERE user_to = ? OR user_from = ? ORDER by id DESC LIMIT 1');
		$stmt->execute([$userLoggedIn, $userLoggedIn]);
		$row_count = $stmt->rowCount();

    //if(mysqli_num_rows($query) == 0)
		if($row_count == 0)
        return false;

    //$row = mysqli_fetch_array($query);
		$row = $stmt->fetch();
    $user_to = $row['user_to'];
    $user_from = $row['user_from'];

    if($user_to != $userLoggedIn)
        return $user_to;
    else
        return $user_from;
  }

  public function sendMessage($user_to, $body, $date) {

      if($body != "") {
          $userLoggedIn = $this->user_obj->getUsername();
          //$query = mysqli_query($this->con, "INSERT INTO messages VALUES(0, '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
					$stmt = $this->spdo->prepare('INSERT INTO messages VALUES(0, ?, ?, ?, ?, ?, ?, ?)');
					$stmt->execute([$user_to, $userLoggedIn, $body, $date, "no", "no", "no"]);
      }

  }

  public function getMessages($otherUser) {
    $userLoggedIn = $this->user_obj->getUsername();
    $data = "";

    //$query = mysqli_query($this->con, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$otherUser'");

		$stmt = $this->spdo->prepare('UPDATE messages SET opened = ? WHERE user_to = ? AND user_from = ?');
		$stmt->execute(["yes", $userLoggedIn, $otherUser]);

    //$get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')");

		$getMsgStmt = $this->spdo->prepare('SELECT * FROM messages WHERE (user_to = ? AND user_from = ?) OR (user_from = ? AND user_to = ?)');
		$getMsgStmt->execute([$userLoggedIn, $otherUser, $userLoggedIn, $otherUser]);

    //while($row = mysqli_fetch_array($get_messages_query)) {
		while($row = $getMsgStmt->fetch()) {
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];
        $body = $row['body'];

        $div_top = ($user_to == $userLoggedIn) ? "<div class='message card' id='one'>" : "<div class='message card' id='two'>";
        $data = $data . $div_top . $body . "</div><br><br>";
    }
    return $data;
  }

  public function getLatestMessages($userLoggedIn, $username) {
      $details_array = array();

      //$query = mysqli_query($this->con, "SELECT body, user_to, date from messages WHERE (user_to='$userLoggedIn' AND user_from='$username') OR (user_to='$username' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1");

			$stmt = $this->spdo->prepare('SELECT body, user_to, date FROM messages WHERE (user_to = ? AND user_from = ?) OR (user_to = ? AND user_from = ?) ORDER BY id DESC LIMIT 1');
			$stmt->execute([$userLoggedIn, $username, $username, $userLoggedIn]);
			$row = $stmt->fetch();

      //$row = mysqli_fetch_array($query);
      $sent_by = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";

	  $date_time = $row['date']; // Time of post
	  $time_message = $this->datetime($date_time);

      // //Timeframe
      // $date_time_now = date("Y-m-d H:i:s");
      // $start_date = new DateTime($row['date']); // Time of post
      // $end_date = new DateTime($date_time_now); // current time
      // $interval = $start_date->diff($end_date);
      // if($interval->y >= 1) {
      //   if($interval == 1)
      //     $time_message = $interval->y . " year ago"; // 1 year ago
      //   else
      //     $time_message = $interval->y . " years ago"; // 1+ year ago
      // }
      // else if ($interval-> m >= 1) {
      //   if($interval->d == 0) {
      //     $days = " ago";
      //   }
      //   else if($interval->d == 1) {
      //     $days = $interval->d . " day ago";
      //   }
      //   else {
      //     $days = $interval->d . " days ago";
      //   }
	  //
      //   if($interval->m == 1){
      //     $time_message = $interval->m . " month " . $days;
      //   } else {
      //     $time_message = $interval->m . " months " . $days;
      //   }
      // }
      // else if($interval->d >= 1) {
      //   if($interval->d == 1) {
      //     $time_message = "Yesterday";
      //   }
      //   else {
      //     $time_message = $interval->d . " days ago";
      //   }
      // }
      // else if($interval->h >= 1) {
      //   if($interval->h == 1) {
      //     $time_message = $interval->h . " hour ago";
      //   }
      //   else {
      //     $time_message = $interval->h . " hours ago";
      //   }
      // }
      // else if($interval->i >= 1) {
      //   if($interval->i == 1) {
      //     $time_message = $interval->i . " minute ago";
      //   }
      //   else {
      //     $time_message = $interval->i . " minutes ago";
      //   }
      // }
      // else {
      //   if($interval->s < 30) {
      //     $time_message = "Just now";
      //   }
      //   else {
      //     $time_message = $interval->s . " seconds ago";
      //   }
      // }

      array_push($details_array, $sent_by);
      array_push($details_array, $row['body']);
      array_push($details_array, $time_message);

      return $details_array;

  }

  public function getConvos() {
      $userLoggedIn = $this->user_obj->getUsername();
      $return_string = "";
      $convos = array();

      $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");

      while($row = mysqli_fetch_array($query)) {
          $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

          if(!in_array($user_to_push, $convos)) {
              array_push($convos, $user_to_push);
          }
      }

      foreach($convos as $username) {
          $user_found_obj = new User($username, $this->spdo);
          $latest_message_details = $this->getLatestMessages($userLoggedIn, $username);

          $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
          $split = str_split($latest_message_details[1], 12);
          $split = $split[0] . $dots;

          $return_string .= "<a href='?inbox&u=$username' class='list-group-item list-group-item-action flex-column align-items-start'>
                              <div class='user_found_messages'>
                              <img src='" . $user_found_obj->getAvatar() . "' class='list-group-avatar'>


                              <div class='d-flex w-100 justify-content-between'>
                              <span class='mb-1'>

                              <small class='text-muted'>
                              To: " . $user_found_obj->getDisplayName() . "</span>
                              " . $latest_message_details[2] . "</small></div>
                              <p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . "</p>
                              </div></a>

                              ";
      }
      return $return_string;

  }

  public function getConvosDropdown($data, $limit) {

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		$convos = array();

		if($page == 1)
				$start = 0;
		else
				$start = ($page - 1) * $limit;

		$set_viewed_query = mysqli_query($this->con, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn'");

		$query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");

		while($row = mysqli_fetch_array($query)) {
				$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

				if(!in_array($user_to_push, $convos)) {
						array_push($convos, $user_to_push);
				}
		}

		$num_iterations = 0; // num messages checked
		$count = 1; // number of messages posted

		foreach($convos as $username) {

				if($num_iterations++ < $start)
						continue;

				if($count > $limit)
						break;
				else {
					$count++;
				}

				$is_unread_query = mysqli_query($this->con, "SELECT opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$username' ORDER BY id DESC");
				$row = mysqli_fetch_array($is_unread_query);
				$style = ($row['opened'] == 'no') ? "background-color: #374925;" : "";

				$user_found_obj = new User($username, $this->spdo);
				$latest_message_details = $this->getLatestMessages($userLoggedIn, $username);

				$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
				$split = str_split($latest_message_details[1], 12);
				$split = $split[0] . $dots;

				$return_string .= " <a href='/?inbox&u=$username' class='list-group-item list-group-item-action flex-column align-items-start'>
														<div class='user_found_messages' style='" . $style . "'>
														<img src='" . $user_found_obj->getAvatar() . "' class='list-group-avatar'>

														<div class='d-flex justify-content-between'>
														<span class='mb-1'>

														<small class='text-muted'>
														To: " . $user_found_obj->getDisplayName() . "</span>
														" . $latest_message_details[2] . "</small></div>
														<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . "</p>
														</div></a>

														";
		}

		//if posts were loaded
		if($count > $limit)
				$return_string .= "<input type='hidden' class='nextPageDropDownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropDownData' value='false'>";
		else {
			$return_string .= "<input type='hidden' class='noMoreDropDownData' value='true'> <p style='text-align: center; margin: 3px;'>No more messages to load.</p>";
		}

		return $return_string;

	}

	public function getUnreadNumber() {
		$userLoggedIn = $this->user_obj->getUsername();
		$query = mysqli_query($this->con, "SELECT * FROM messages WHERE viewed='no' AND user_to='$userLoggedIn'");
		return mysqli_num_rows($query);
	}

}
?>