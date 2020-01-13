<?php
namespace zen3mp;

class User {
	private $user;
	private $spdo;

	public function __construct($user, $spdo){
		$this->spdo = $spdo;
		$stmt = $this->spdo->prepare('SELECT * FROM users WHERE username = ?');
		$stmt->execute([$user]);
		$this->user = $stmt->fetch();
	}

	public function getUsername() {
		return $this->user['username'];
	}

	public function getUserID() {
		return $this->user['id'];
	}

	public function getNumPosts() {
		return $this->user['num_posts'];
	}

	public function getNumLikes() {
		return $this->user['num_likes'];
	}

	public function getRank() {
		return $this->user['user_title'];
	}

	public function getDisplayName() {
		return $this->user['displayname'];
	}

	public function getAvatar() {
		return $this->user['avatar'];
	}

	public function getFriendList() {
		return $this->user['friend_list'];
	}

	public function getNumberOfFriendRequests() {
		$stmt = $this->spdo->prepare('SELECT COUNT(*) FROM friend_requests WHERE user_to = ?');
		$stmt->execute([$this->getUsername()]);
		$num_rows = $stmt->fetchColumn();
		return $num_rows;
	}

	public function getNumberOfFriends() {
	    $num_friends = (substr_count($this->user['friend_list'], ",")) - 1;
		return $num_friends;
    }
    
    public function getNewestUserUN() {
        $stmt = $this->spdo->prepare('SELECT username FROM users WHERE verify_user = ? ORDER BY signup_date DESC ');
        $stmt->execute(["yes"]);
        $user = $stmt->fetchColumn();
        return $user;
    }

    public function getNewestUserDN() {
        $stmt = $this->spdo->prepare('SELECT username FROM users WHERE verify_user = ? ORDER BY signup_date DESC ');
        $stmt->execute(["yes"]);
        $user = $stmt->fetchColumn();
        return $user;
    }

    public function getNumActiveUsers() {
        $stmt = $this->spdo->prepare('SELECT COUNT(*) from users WHERE verify_user = ?');
        $stmt->execute(["yes"]);
        $num = $stmt->fetchColumn();
        return $num;
    }

	public function isClosed() {
		$username = $this->user['username'];
		$stmt = $this->spdo->prepare('SELECT user_closed FROM users WHERE username = ?');
		$stmt->execute([$this->getUsername()]);
		$row = $stmt->fetch();

		if($row['user_closed'] == 'yes')
		  return true;
		else
		  return false;
	}

	public function isFriend($username_to_check) {
		$usernameComma = "," . $username_to_check . ",";

		if(strstr($this->user['friend_list'], $usernameComma) || $username_to_check == $this->user['username']) {
		  return true;
		} else {
		  return false;
		}
	}

	// ==================== Friend Requests ========================

	public function didReceiveRequest($user_from) {
		$user_to = $this->getUsername();
		$stmt = $this->spdo->prepare('SELECT COUNT(*) FROM friend_requests WHERE user_to = ? AND user_from = ?');
		$stmt->execute([$user_to, $user_from]);
		$num_rows = $stmt->fetchColumn();

		if($num_rows > 0) {
		  return true;
		} else {
		  return false;
		}
	}

	public function didSendRequest($user_to) {
		$user_from = $this->getUsername();
		$stmt = $this->spdo->prepare('SELECT COUNT(*) FROM friend_requests WHERE user_to = ? AND user_from = ?');
		$stmt->execute([$user_to, $user_from]);
		$num_rows = $stmt->fetchColumn();

		if($num_rows > 0) {
		  return true;
		} else {
		  return false;
		}
	}

	public function removeFriend($user_to_remove) {
		$logged_in_user = $this->getUsername();
		$stmt = $this->spdo->prepare('SELECT friend_array FROM users WHERE username = ?');
		$stmt->execute([$user_to_remove]);
		$row = $stmt->fetch();
		$friend_array_username = $row['friend_list'];

		$new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_list']);
		$remove_friend = $this->spdo->prepare('UPDATE users SET friend_list = ? WHERE username = ?');
		$remove_friend->execute([$new_friend_array, $logged_in_user]);

		$new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
		$remove_friend = $this->spdo->prepare('UPDATE users SET friend_list = ? WHERE username = ?');
		$remove_friend->execute([$new_friend_array, $user_to_remove]);
	}

	public function sendRequest($user_to) {
		$user_from = $this->getUsername();
		$stmt = $this->spdo->prepare('INSERT INTO friend_requests VALUES(0, ?, ?)');
		$stmt->execute([$user_to, $user_from]);
	}

	public function getMutualFriends($user_to_check) {
		$mutualFriends = 0;
  		$user_array = $this->user['friend_list'];
  		$user_array_explode = explode(",", $user_array);

		$stmt = $this->spdo->prepare('SELECT friend_list FROM users WHERE username = ?');
		$stmt->execute([$user_to_check]);
		$row = $stmt->fetch();

  		$user_to_check_array = $row['friend_list'];
  		$user_to_check_array_explode = explode(",", $user_to_check_array);

		foreach($user_array_explode as $i) {

			foreach($user_to_check_array_explode as $j) {

			    if($i == $j && $i != "") {
			        $mutualFriends++;
			    }
			}
		}
		return $mutualFriends;
    }


	public function getUserStyle() {
		$user_id = $this->user['id'];
		$stmt = $this->spdo->prepare('SELECT style FROM users_about WHERE user_id = ?');
		$stmt->execute([$user_id]);
		$row = $stmt->fetch();
		$style = $row['style'];
		return $style;
	}

	public function getTrends() {
		$stmt = $this->spdo->prepare('SELECT * FROM trends ORDER BY hits DESC LIMIT 10');
		$stmt->execute();
		$row = $stmt->fetch();

		foreach ($stmt as $row) {

			$word = $row['title'];
			$word_dot = strlen($word) >= 14 ? "..." : "";

			$trimmed_word = str_split($word, 14);
			$trimmed_word = $trimmed_word[0];

			echo "<li class='trending-list-group-item'>";
			echo $trimmed_word . $word_dot;
			echo "</li><br>";

		}
	}

	public function listUsers() {

		$stmt = $this->spdo->prepare('SELECT * FROM users WHERE user_closed = ? ORDER BY id ASC');
		$stmt->execute(["no"]);

		echo '<div class="row" style="justify-content: flex-start !important;">';

		while($row = $stmt->fetch()) {

		    $num_friends_ulist = (substr_count($row['friend_list'], ",")) - 1;
		    echo '<div class="card" style="width: 18rem; border: 1px solid black; margin: 2px;  overflow: hidden; flex-grow: 1;">
		          <img class="card-img-top" src="' . $row['header_img'] . '" alt="Card image cap" style="height: 185px;">
		          <div class="card-body">
		          <img src="' . $row['avatar'] . '" class="avatar">
		          <h5 class="card-title"><a href="' . $row['username'] . '">' . $row['username'] . '</a></h5>
		          <p class="card-text">Rank: '. $row['user_title'] . '
		          <br>Posts: ' . $row['num_posts'] . '
		          <br>Likes: ' . $row['num_likes'] . '
		          <br>Friends: ' . $num_friends_ulist . '
		          <br>Date Joined: ' . $row['signup_date'] . '</p>
		          </div>
		          </div>
		          ';
		}
		echo '</div>';

	}

	public function aboveButton() {
		$rank = $this->user['user_title'];
		$array = array("Admin", "God");
		if (in_array($rank, $array)) {
			echo "<a href='/above/' title='Above'><i class='typcn typcn-weather-cloudy icon btnPurp'></i></a>";
		} else {
			echo "";
		}
	}

	public function getUserAboutDetails() {
		$user_id = $this->user['id'];
		$stmt = $this->spdo->prepare('SELECT * FROM users_about WHERE user_id = ?');
		$stmt->execute([$user_id]);

		while ($row = $stmt->fetch()) {

			$userBio = $row['bio'];
			$userWebsite = $row['website'];
			$userTwitter = $row['twitter'];
			$userYouTube = $row['youtube'];
			$userTwitch = $row['twitch'];
			$userDiscord = $row['discord'];
			$userGithub = $row['github'];
			$userSwitch = $row['switch'];

			if($userBio != "")
					echo "<div class='alert alert-primary' role='alert'>$userBio</div>";

			echo "<ul class='list-group'>";

			if($userWebsite != "")
					echo "<li class='list-group-item'><i class='typcn typcn-attachment btnPurp icon'></i> <a href='http://$userWebsite'>$userWebsite</a></li>";
			if($userTwitter != "")
					echo "<li class='list-group-item'><i class='typcn typcn-social-twitter btnPurp icon'></i> <a href='https://twitter.com/$userTwitter'>$userTwitter</a></li>";
			if($userYouTube != "")
					echo "<li class='list-group-item'><i class='typcn typcn-social-youtube btnPurp icon'></i> <a href='https://youtube.com/$userYouTube'>$userYouTube</a></li>";
			if($userTwitch != "")
					echo "<li class='list-group-item'><i class='fab fa-twitch btnPurp'></i> <a href='https://twitch.tv/$userTwitch'>$userTwitch</a></li>";
			if($userGithub != "")
					echo "<li class='list-group-item'><i class='fab fa-github btnPurp'></i> <a href='https://github.com/$userGithub'>$userGithub</a></li>";
			if($userSwitch != "")
					echo "<li class='list-group-item'><i class='fab fa-nintendo-switch btnPurp'></i>$userSwitch</a></li>";
			if($userDiscord != "")
					echo "<li class='list-group-item'><i class='fab fa-discord btnPurp'></i> $userDiscord</li>";

			echo "</ul>";

		}
	}

	// ==================== Authentication =========================

	public function getTokenByUser($username, $expired) {
		$stmt = $this->spdo->prepare('SELECT * from token_auth where username = ? and is_expired = ?');
		$stmt->execute([$username, $expired]);
		$result = $stmt->fetch();
		return $result;
	}

	public function insertToken($username, $random_password_hash, $random_selector_hash, $expiry_date) {
		$stmt = $this->spdo->prepare('INSERT INTO token_auth (username, password_hash, selector_hash, expiry_date) values (?, ?, ?,?)');
		$stmt->execute([$username, $random_password_hash, $random_selector_hash, $expiry_date]);
		$result = $stmt->fetch();
		return $result;
	}

	public function markExpired($tokenID) {
		$expired = 1;
		$stmt = $this->spdo->prepare('UPDATE token_auth SET is_expired = ? WHERE id = ?');
		$stmt->execute([$expired, $tokenID]);
		$result = $stmt->fetch();
		return $result;
	}


}
?>
