<?php
namespace zen3mp;
class Board {
    private $spdo;

    public function __construct($user, $spdo) {
        $this->spdo = $spdo;
        $this->user_obj = new User($user, $spdo);
    }

    // Category

    public function getBoardsByCategory($id) {
        $stmt = $this->spdo->prepare('SELECT * FROM boards WHERE category = ?');
        $stmt->execute([$id]);
        $boards = $stmt->rowCount();
        return $boards;
    }

    public function getCategoryName($id) {
        $stmt = $this->spdo->prepare('SELECT name FROM category WHERE id = ? AND hidden = ?');
        $stmt->execute([$id, 0]);
        $name = $stmt->fetchColumn();
        return $name;
    }

    public function getCategoryID($id) {
        $stmt = $this->spdo->prepare('SELECT category FROM boards WHERE id = ?');
        $stmt->execute([$id]);
        $id = $stmt->fetchColumn();
        return $id;
    }

    // Board

    public function getBoardID($id) {
        $stmt = $this->spdo->prepare('SELECT id FROM boards WHERE category = ?');
        $stmt->execute([$id]);
        $id = $stmt->fetchColumn();
        return $id;
    }

    public function getBoardTitle($id) {
        $stmt = $this->spdo->prepare('SELECT title FROM boards WHERE id = ?');
        $stmt->execute([$id]);
        $name = $stmt->fetchColumn();
        return $name;
    }

    public function getBoardDesc($id) {
        $stmt = $this->spdo->prepare('SELECT description FROM boards WHERE id = ?');
        $stmt->execute([$id]);
        $desc = $stmt->fetchColumn();
        return $desc;
    }

    public function checkBoardLocked($id) {
        $stmt = $this->spdo->prepare('SELECT locked FROM boards WHERE id = ?');
        $stmt->execute([$id]);
        $locked = $stmt->fetchColumn();
        return $locked;
    }

    public function getBoardCategory($id) {
        $stmt = $this->spdo->prepare('SELECT category FROM boards WHERE id = ?');
        $stmt->execute([$id]);
        $category = $stmt->fetchColumn();
        return $category;
    }

    public function getTopicsByCategory($id) {
        $stmt = $this->spdo->prepare('SELECT * FROM topics WHERE category_id = ? AND reply_to = ?');
        $stmt->execute([$id, 0]);
        $row = $stmt->rowCount();
        return $row;
    }

    public function getNumBoards() {
        $stmt = $this->spdo->prepare('SELECT COUNT(*) FROM boards');
        $stmt->execute();
        $num = $stmt->fetchColumn();
        return $num;
    }

    // Replies

    public function getNumReplies($id) {
        $stmt = $this->spdo->prepare('SELECT * FROM topics WHERE reply_to = ?');
        $stmt->execute([$id]);
        $row = $stmt->rowCount();
        return $row;
    }

    public function getReplyID($id) {
        $stmt = $this->spdo->prepare('SELECT id FROM topics WHERE reply_to = ?');
        $stmt->execute([$id]);
        $id_array = array();
        while ($row = $stmt->fetch()) 
        {
            array_push($id_array, $row["id"]);
        }
        return $id_array;
    }

    public function getReplyBody($id, $reply_to) {
        $stmt = $this->spdo->prepare('SELECT body FROM topics WHERE id = ? AND reply_to = ?');
        $stmt->execute([$id, $reply_to]);
        $row = $stmt->fetchColumn();
        return $row;
    }

    public function getReplyOP($id, $reply_to) {
        $stmt = $this->spdo->prepare('SELECT op FROM topics WHERE id = ? AND reply_to = ?');
        $stmt->execute([$id, $reply_to]);
        $row = $stmt->fetchColumn();
        return $row;
    }

    public function getReplyDate($id, $reply_to) {
        $stmt = $this->spdo->prepare('SELECT date_added FROM topics WHERE id = ? AND reply_to = ?');
        $stmt->execute([$id, $reply_to]);
        $row = $stmt->fetchColumn();
        return $row;
    }

    public function getTopicTitle($id) {
        $stmt = $this->spdo->prepare('SELECT title FROM topics WHERE id = ? AND reply_to = ?');
        $stmt->execute([$id, 0]);
        $title = $stmt->fetchColumn();
        return $title;
    }

    public function getTopicDate($id) {
        $stmt = $this->spdo->prepare('SELECT date_added FROM topics WHERE id = ? AND reply_to = ?');
        $stmt->execute([$id, 0]);
        $date = $stmt->fetchColumn();
        return $date;
    }

    public function getTopicBody($id) {
        $stmt = $this->spdo->prepare('SELECT body FROM topics WHERE id = ? AND reply_to = ?');
        $stmt->execute([$id, 0]);
        $body = $stmt->fetchColumn();
        return $body;
    }

    public function getTopicCategory($id) {
        $stmt = $this->spdo->prepare('SELECT category_id FROM topics WHERE id = ? AND reply_to = ?');
        $stmt->execute([$id, 0]);
        $category = $stmt->fetchColumn();
        return $category;
    }

    public function getTopicOP($id) {
        $stmt = $this->spdo->prepare('SELECT op FROM topics WHERE id = ?');
        $stmt->execute([$id]);
        $op = $stmt->fetchColumn();
        return $op;
    }

    public function getAddedByID($username) {
        $user_obj = new User($username, $this->spdo);
        return $user_obj->getUserID();
    }

    public function getAddedByAvatar($username) {
        $user_obj = new User($username, $this->spdo);
        return $user_obj->getAvatar();
    }

    public function checkForBoard($id) {
        $stmt = $this->spdo->prepare('SELECT id FROM boards WHERE id = ?');
        $stmt->execute([$id]);
        $check = $stmt->fetch();
        if($check) {
            
        } else {
            header("Location:https://zeniea.com/forum/");
        }
    }

    public function getPostsByCategory($id) {
        $posts = array();
        $stmt = $this->spdo->prepare('SELECT id FROM topics WHERE category_id = ? AND reply_to = ?');
        $stmt->execute([$id, 0]);
        while($row = $stmt->fetch()) 
        {
            $id = $row['id'];
            array_push($posts,$id);
        }
        return $posts;
    }

    public function postNewTopic($title, $body, $category, $sticky, $reply_to) {

        $body = strip_tags($body);
        $check_empty = preg_replace('/\s+/', '', $body); //Deltes all spaces

        if($check_empty != "") {

			$body_array = preg_split("/\s+/", $body);

			foreach($body_array as $key => $value) {

				if(strpos($value, "www.youtube.com/watch?v=") !== false) {

					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe width=\'560\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
					$body_array[$key] = $value;

				}

			}
			$body = implode(" ", $body_array);

			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

            $stmt = $this->spdo->prepare('INSERT INTO topics VALUES(0, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$reply_to, $added_by, $title, $body, $category, $date_added, $sticky]);
            header("Location: https://zeniea.com/forum/?board=" . $category);
        }

    }

    public function postReply($body, $reply_to) {
        $title = "Reply";
        $sticky = 0;
        $body = strip_tags($body);
        $check_empty = preg_replace('/\s+/', '', $body); //Deltes all spaces

        if($check_empty != "") {

			$body_array = preg_split("/\s+/", $body);

			foreach($body_array as $key => $value) {

				if(strpos($value, "www.youtube.com/watch?v=") !== false) {

					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe width=\'560\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
					$body_array[$key] = $value;

				}

			}
			$body = implode(" ", $body_array);

			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

            $stmt = $this->spdo->prepare('INSERT INTO topics VALUES(0, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$reply_to, $added_by, $title, $body, $category, $date_added, $sticky]);
            header("Location: https://zeniea.com/forum/?board=" . $category);
        }
    }


}