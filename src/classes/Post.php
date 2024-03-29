<?php
namespace zen3mp;
use \Datetime;

class Post 
{
    private $user_obj;
    private $spdo;
    private $rpdo;

    public function __construct($user, $spdo, $rpdo) 
    {
        $this->userLoggedIn = $user;
        $this->user_obj = new User($user, $spdo);
        $this->username = $this->user_obj->getUsername();
        $this->character = "Placeholder";
        $this->spdo = $spdo;
        $this->rpdo = $rpdo;
    }
    
    public function getPostBody($id)
    {
        $stmt = $this->spdo->prepare('SELECT body FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $body = $stmt->fetchColumn();
        return $body;
    }

    public function getPostAddedBy($id)
    {
        $stmt = $this->spdo->prepare('SELECT added_by FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $added_by = $stmt->fetchColumn();
        return $added_by;
    }

    public function getPostUserTo($id)
    {
        $stmt = $this->spdo->prepare('SELECT user_to FROM posts WHERE id = ?');
        $stmt->excute([$id]);
        $user_to = $stmt->fetchColumn();
        return $user_to;
    }

    public function getPostDateAdded($id)
    {
        $stmt = $this->spdo->prepare('SELECT date_added FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $date_added = $stmt->fetchColumn();
        return $date_added;
    }

    public function getPostLikes($id)
    {
        $stmt = $this->spdo->prepare('SELECT likes FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $likes = $stmt->fetchColumn();
        return $likes;
    }

    public function getPostFileName($id)
    {
        $stmt = $this->spdo->prepare('SELECT file_name FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $file_name = $stmt->fetchColumn();
        return $file_name;
    }

    public function getPostFileType($id)
    {
        $stmt = $this->spdo->prepare('SELECT file_type FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $file_type = $stmt->fetchColumn();
        return $file_type;
    }

    public function getNumPosts()
    {
        $stmt = $this->spdo->prepare('SELECT COUNT(*) FROM posts WHERE deleted = ?');
        $stmt->execute(["no"]);
        $num_posts = $stmt->fetchColumn();
        return $num_posts;
    }

    public function getNumComments()
    {
        $stmt = $this->spdo->prepare('SELECT COUNT(*) FROM comments WHERE removed = ?');
        $stmt->execute(["no"]);
        $num_posts = $stmt->fetchColumn();
        return $num_posts;
    }

    public function doesUserExist($username)
    {
        $stmt = $this->spdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $exist = $stmt->fetchColumn();
        if ($exist > 0)
            return True;
        else
            return False; 
    }

    public function submitMediaPost($user_to, $file_name, $file_type) 
    {
		$date_added = date("Y-m-d H:i:s");
		$added_by = $this->user_obj->getUsername();

		// If user is on own profile, user_to is 'none'
		if($user_to == $added_by) {
			$user_to = "none";
		}

		$stmt = $this->spdo->prepare('INSERT INTO posts VALUES(0, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$stmt->execute([" ", $added_by, $user_to, $date_added, "no", "no", "0", $file_name, $file_type]);
		$returned_id = $this->spdo->lastInsertId();

		// Insert notification
		if($user_to != 'none') {
			$notification = new Notification($added_by, $this->character, $this->spdo, $this->rpdo);
			$notification->insertNotification($returned_id, $user_to, "profile_post");
		}

		// Update post count for user
		$num_posts = $this->user_obj->getNumPosts();
		$num_posts++;
		$update_query = $this->spdo->prepare('UPDATE users SET num_posts = ? WHERE username = ?');
		$update_query->execute([$num_posts, $added_by]);

	}

    public function submitNewsPost($title, $body, $added_by, $category) 
    {
        $date_added = date("Y-m-d H:i:s");
        $qry = $this->spdo->prepare('SELECT MAX(post_id) FROM news_posts WHERE category = ?');
        $qry->execute([$category]);
        $post_id = $qry->fetchColumn();

        if ($post_id == 0)
            $post_id = 1;
        else
            $post_id++;

		$stmt = $this->spdo->prepare('INSERT INTO news_posts VALUES(0, ?, ?, ?, ?, ?, ?, ?)');
		$stmt->execute([$post_id, $title, $body, $added_by, $date_added, "0", $category]);
	}

    public function submitPost($body, $user_to, $imageName, $file_type) 
    {

		$body = strip_tags($body); // removes html tags
		$check_empty = preg_replace('/\s+/', '', $body); // Deletes all spaces

        if($check_empty != "") 
        {

			$body_array = preg_split("/\s+/", $body);

            foreach($body_array as $key => $value) 
            {
                
                if (strpos($value, "www.youtube.com/watch?v=") !== false) 
                {
					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><br><iframe width=560 height=315 src=" . $value ."></iframe><br>";
					$body_array[$key] = $value;
                }

                if (strpos($value, "@") !== false)
                {
                    $usernameArray = explode("@", $value);
                    if ($this->doesUserExist($usernameArray) == true)
                    {
                        $value = "<a href='https://zeniea.com/" . $usernameArray[1] . "'>@" . $usernameArray[1] . "</a>";
                        $body_array[$key] = $value;
                    }
                    else
                    {
                        $value = "@" . $usernameArray[1];
                        $body_array[$key] = $value;
                    }
                    
                    // dick
                }
                
            }
            
			$body = implode(" ", $body_array);

			// current date and time
			$date_added = date("Y-m-d H:i:s");
			// get username
			$added_by = $this->user_obj->getUsername();

			// if user is on own profile, user_to is 'none'
			if ($user_to == $added_by) {
				$user_to = "none";
            }

			// insert post
			$stmt = $this->spdo->prepare('INSERT INTO posts VALUES(0, ?, ?, ?, ?, ?, ?, ?, ?, ? )');
			$stmt->execute([$body, $added_by, $user_to, $date_added, "no", "no", "0", $imageName, $file_type]);
            $returned_id = $this->spdo->lastInsertId();

            // insert notification
            $notification = new Notification($added_by, $this->character, $this->spdo, $this->rpdo);
            
            // calculate gold for post
            // $character = new Character($this->username, $this->spdo, $this->rpdo);
            // $checkForChar = $character->checkForCharacter();
            // if ($checkForChar === true) 
            // { 
            //     $char_luck = $character->getCharacterLuck();
            //     $string_bytes = strlen($body);

            //     if ($string_value < 250)
            //         $randkey = rand(4, 8);
            //     else
            //         $randkey = rand(6, 10);

            //     if ($imageName)
            //         $picture = rand(1, 15);
            //     else 
            //         $picture = 0;
                
            //     $string_value = $string_bytes / $randkey;
            //     $luck_bonus = pow(($char_luck / $randkey), 2);
            //     $gold = ceil($string_value + $luck_bonus + $picture);
            //     $character->setCharacterMoney($gold);
            //     $notification->insertRPGNotification($returned_id, $added_by, "gold_added", $gold);
            // }

			if ($user_to != 'none') {
                $notification->insertNotification($returned_id, $user_to, "profile_post");
			}

			//Update post count for user
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = $this->spdo->prepare('UPDATE users SET num_posts = ? WHERE username = ?');
			$update_query->execute([$num_posts, $added_by]);

			$stopWords = "a about above across after again against all almost alone along already
			 also although always among am an and another any anybody anyone anything anywhere are
			 area areas around as ask asked asking asks at away b back backed backing backs be became
			 because become becomes been before began behind being beings best better between big
			 both but by c came can cannot case cases certain certainly clear clearly come could
			 d did differ different differently do does done down down downed downing downs during
			 e each early either end ended ending ends enough even evenly ever every everybody
			 everyone everything everywhere f face faces fact facts far felt few find finds first
			 for four from full fully further furthered furthering furthers g gave general generally
			 get gets give given gives go going good goods got great greater greatest group grouped
			 grouping groups h had has have having he her here herself high high high higher
			 highest him himself his how however i im if important in interest interested interesting
			 interests into is it its itself j just k keep keeps kind knew know known knows
			 large largely last later latest least less let lets like likely long longer
			 longest m made make making man many may me member members men might more most
			 mostly mr mrs much must my myself n necessary need needed needing needs never
			 new new newer newest next no nobody non noone not nothing now nowhere number
			 numbers o of off often old older oldest on once one only open opened opening
			 opens or order ordered ordering orders other others our out over p part parted
			 parting parts per perhaps place places point pointed pointing points possible
			 present presented presenting presents problem problems put puts q quite r
			 rather really right right room rooms s said same saw say says second seconds
			 see seem seemed seeming seems sees several shall she should show showed
			 showing shows side sides since small smaller smallest so some somebody
			 someone something somewhere state states still still such sure t take
			 taken than that the their them then there therefore these they thing
			 things think thinks this those though thought thoughts three through
			 thus to today together too took toward turn turned turning turns two
			 u under until up upon us use used uses v very w want wanted wanting
			 wants was way ways we well wells went were what when where whether
			 which while who whole whose why will with within without work
			 worked working works would x y year years yet you young younger
             youngest your yours z lol haha omg hey ill iframe wonder else like
             day cool damn 123 1 2 3 4 5 6 7 8 9 10 69 666 420 hello hi pls please
			 hate sleepy reason for some little yes bye choose nigger penis fuck shit dick piss bitch";

			//Convert stop words into array - split at white space
			$stopWords = preg_split("/[\s,]+/", $stopWords);

			//Remove all punctionation
			$no_punctuation = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);

			//Predict whether user is posting a url. If so, do not check for trending words
			if (strpos($no_punctuation, "height") === false && strpos($no_punctuation, "width") === false
                && strpos($no_punctuation, "http") === false && strpos($no_punctuation, "youtube") === false)
            {
				//Convert users post (with punctuation removed) into array - split at white space
				$keywords = preg_split("/[\s,]+/", $no_punctuation);

				foreach($stopWords as $value) {
					foreach($keywords as $key => $value2){
						if(strtolower($value) == strtolower($value2))
							$keywords[$key] = "";
					}
				}

				foreach ($keywords as $value) {
						$this->calculateTrend(ucfirst($value));
				}

			}

		}
	}

    public function calculateTrend($term) 
    {
        if ($term != '') 
        {
			$stmt = $this->spdo->prepare('SELECT COUNT(*) FROM trends WHERE title = ?');
			$stmt->execute([$term]);
			$num_rows = $stmt->fetchColumn();

			if($num_rows == 0) {
                $insert_query = $this->spdo->query("INSERT INTO trends(title,hits) VALUES('$term','1')");
			} else {
                $insert_query = $this->spdo->query("UPDATE trends SET hits=hits+1 WHERE title='$term'");
            }
		}

	}

	public function loadPostsFriends($data, $limit) 
	{
		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;


        $str = ""; //String to return

		$data_query = $this->spdo->prepare('SELECT * FROM posts WHERE deleted = ? ORDER BY id DESC');
		$data_query->execute(["no"]);
		$num_query = $this->spdo->prepare('SELECT COUNT(*) FROM posts WHERE deleted = ?');
  		$num_query->execute(["no"]);
		$num_rows = $num_query->fetchColumn();

        if($num_rows > 0) 
        {
	        $num_iterations = 0;  //Number of results checked (not necessaryily posted)
	        $count = 1;

            while ($row = $data_query->fetch())  
            {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
                $date_time = $row['date_added'];
                $file_name = $row['file_name'];
                $file_type = $row['file_type'];

				//Prepare user_to string so  it can included even if not posted
				if ($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($row['user_to'], $this->spdo);
					$user_to_name = $user_to_obj->getDisplayName();
					$user_to = "to <a href=" . $row['user_to'] . ">" . $row['user_to'] . "</a>";
				}

				//Check if user who posted, has their account closed
				$added_by_obj = new User($added_by, $this->spdo);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($userLoggedIn, $this->spdo);
				if($user_logged_obj->isFriend($added_by)){

					if($num_iterations++ < $start)
						continue;

					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

		            // Delete Post Button
					if($userLoggedIn == $added_by)
						$delete_button = "<button class='btn btn-danger close' id='post$id'><i class='typcn typcn-delete icon' style='font-size: 24px; margin: 5px;'></i></button>";
					else
						$delete_button = "";

					$user_details_query = $this->spdo->prepare('SELECT displayname, avatar FROM users WHERE username = ?');
					$user_details_query->execute([$added_by]);
					$user_row = $user_details_query->fetch();
					$displayname = $user_row['displayname'];
					$avatar = $user_row['avatar'];

          		?>

				<script>
				function toggle<?php echo $id; ?>() 
                {
					var element = document.getElementById("toggleComment<?php echo $id; ?>");

					if (element.style.display == "block")
						element.style.display = "none";
					else
						element.style.display = "block";
				}
				</script>

	          	<?php

		        // check how many comments
				$comments_check = $this->spdo->prepare('SELECT COUNT(*) FROM comments WHERE post_id = ?');
				$comments_check->execute([$id]);
				$comments_check_num = $comments_check->fetchColumn();

		        // timeframe
				$time_message = $this->datetime($date_time);

                // new file upload routine 
                if ($file_type == "png" || $file_type == "jpeg" || $file_type == "jpg" || $file_type == "gif")
                {
                    $file_insert = "<div class='card-img'><img src='$file_name' class='post_image'></div>";
                } 
                else if ($file_type == "mp4" || $file_type == "avi" || $file_type == "mkv") 
                {
                    $file_insert = "<div class='w-100'><video class='post_image' controls>
                                        <source src='https://zeniea.com/$file_name'>
                                        Your browser does not support the video tag.
                                        </video> 
                                    </div>";
                }
                else {
                    $file_insert = "";
                }

				$str .= "<div class='status_post'>
							<div class='card'>
							<div class='card-body' style='padding: 5px; margin: 5px;'>
								<a href='$added_by'>
								<img src='$avatar' class=avatar title='$added_by'>
								</a>
							<h5 class='card-title' style='margin-bottom: 0;'>
									<a href='$added_by'>$displayname</a> $user_to
									<span class='postTime'><a href='post.php?id=$id'>$time_message</a> $delete_button</span>
							</h5>
							<p class='card-text'>$body<br>$file_insert</p>
							<div class='widgets'>
								<a onClick='javascript:toggle$id()' class='btn btn-primary card-link'>
									<i class='typcn typcn-arrow-back icon'> Replies
									<span class='badge badge-primary'>$comments_check_num</span></i></a>
									<span class='likes'><iframe src='src/forms/like_form.php?post_id=$id' scrolling='no'></iframe></span>
							</div>
							</div>
							</div>
						</div>
						<p align='center'>
						<div class='post_comment' id='toggleComment$id' style='display:none; width: 100%; overflow:auto; height: auto;'>
                            <iframe src='lib/pages/comment_frame.php?post_id=$id' id='comment_iframe' frameborder=0 width=100%></iframe>
                            <!-- style='overflow:visible; height: 200px;' -->
						</div>
						</p>
						";
	            }
	            ?>

	            <script>
	            // Delete Post Modal Box
	            $(document).ready(function () {
	              $('#post<?php echo $id; ?>').on('click', function () {
	                // Confirm box
	                bootbox.confirm("Are you sure want to delete?", function (result) {
	                  if (result) {
	                    // AJAX Request
	                    $.ajax({
	                      url: 'src/forms/delete_post.php?post_id=<?php echo $id; ?>',
	                      type: 'POST',
	                      data: {
	                        result: result
	                      },
	                      success: function success(response) {
	                        window.location.reload();
	                      }
	                    });
	                  }
	                });
	              });
	            });
	            </script>

	            <?php
				} //End while loop

				if($count > $limit)
					$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
								<input type='hidden' class='noMorePosts' value='false'>";
				else
					$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'> No more posts to show! </p>";
			}

			echo $str;

	}

    public function loadProfilePosts($data, $limit) 
    {
	    $page = $data['page'];
	    $profileUser = $data['profileUsername'];
	    $userLoggedIn = $this->user_obj->getUsername();

	    if($page == 1)
			$start = 0;
	    else
			$start = ($page - 1) * $limit;

	    $str = "";
		$data_query = $this->spdo->prepare('SELECT * FROM posts WHERE deleted = ? and ((added_by = ? AND user_to = ?) OR user_to = ?) ORDER BY id DESC');
		$data_query->execute(['no', $profileUser, 'none', $profileUser]);
		$num_rows = $data_query->rowCount();

	    if($num_rows > 0) {

	        $num_iterations = 0;  //Number of results checked (not necessaryily posted)
	        $count = 1;


            while ($row = $data_query->fetch())  
            {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
                $file_name = $row['file_name'];
                $file_type = $row['file_type'];

				if($num_iterations++ < $start)
					continue;

				//Once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				}
				else {
					$count++;
				}

				//Prepare user_to string so  it can included even if not posted
                if ($row['user_to'] == "none") 
                {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($row['user_to'], $this->spdo);
					$user_to_name = $user_to_obj->getDisplayName();
					$user_to = "to <a href=" . $row['user_to'] . ">" . $row['user_to'] . "</a>";
				}

				// Delete Post Button
				if ($userLoggedIn == $added_by)
				    $delete_button = "<button class='btn btn-danger close' id='post$id'><i class='typcn typcn-delete icon' style='font-size: 24px; margin: 5px;'></i></button>";
				else
				    $delete_button = "";

				$user_details_query = $this->spdo->prepare('SELECT displayname, avatar FROM users WHERE username = ?');
				$user_details_query->execute([$added_by]);
				$user_row = $user_details_query->fetch();
				///$user_details_query = mysqli_query($this->con, "SELECT displayname, avatar FROM users WHERE username='$added_by'");
				///$user_row = mysqli_fetch_array($user_details_query);
				$displayname = $user_row['displayname'];
				$avatar = $user_row['avatar'];

	          	?>
		        <script>
					function toggle<?php echo $id; ?>() {

						var element = document.getElementById("toggleComment<?php echo $id; ?>");

						if(element.style.display == "block")
							element.style.display = "none";
						else
							element.style.display = "block";
		        	}
		        </script>
	          	<?php

				//Check how many comments
				$comments_check = $this->spdo->prepare('SELECT COUNT(*) FROM comments WHERE post_id = ?');
				$comments_check->execute([$id]);
				$comments_check_num = $comments_check->fetchColumn();
				///$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
				///$comments_check_num = mysqli_num_rows($comments_check);

	          	//Timeframe
				$time_message = $this->datetime($date_time);

                // new file upload routine 
                if ($file_type == "png" || $file_type == "jpeg" || $file_type == "jpg" || $file_type == "gif")
                {
                    $file_insert = "<div class='card-img'><img src='$file_name' class='post_image'></div>";
                } 
                else if ($file_type == "mp4" || $file_type == "avi" || $file_type == "mkv") 
                {
                    $file_insert = "<div class='w-100'><video class='post_image' controls>
                                        <source src='https://zeniea.com/$file_name'>
                                        Your browser does not support the video tag.
                                        </video> 
                                    </div>";
                }
                else {
                    $file_insert = "";
                }

                if ($this->userLoggedIn == "Guest")
                    $widget = "<span class='mt-2'></span>";
                else 
                {
                    $widget = "<div class='widgets'>
                                    <a onClick='javascript:toggle$id()' class='btn btn-primary card-link'>
                                        <i class='typcn typcn-arrow-back icon'> 
                                            Replies <span class='badge badge-primary'>
                                                        $comments_check_num
                                                    </span>
                                        </i>
                                    </a>
                                    <span class='likes'>
                                        <iframe src='src/forms/like_form.php?post_id=$id' scrolling='no'></iframe>
                                    </span>
                                </div>";
                }
	            $str .= "<div class='status_post'>
	                    <div class='card'>
	                    <div class='card-body' style='padding: 5px; margin: 5px;'>
	                      <a href='$added_by'>
	                      <img src='$avatar' width='50' class=avatar title='$added_by'>
	                      </a>
	                    <h4 class='card-title'>
	                        <a href='$added_by'>$displayname</a> $user_to
	                        <span class='postTime'><a href='post.php?id=$id'>$time_message</a> $delete_button</span>
	                    </h4>
	                    <p class='card-text'>$body<br>$file_insert</p>
                                $widget
	                    </div>
	                    </div>
	                  </div>
	                  <p align='center'>
	                  <div class='post_comment' id='toggleComment$id' style='display:none;width: 100%;'>
	                    <iframe src='lib/pages/comment_frame.php?post_id=$id' id='comment_iframe' frameborder=0  width=100%></iframe>
	                  </div>
	                  </p>
	                  ";
	            ?>
	            <script>
	            // Delete Post Modal Box
	            $(document).ready(function () {
	              $('#post<?php echo $id; ?>').on('click', function () {
	                // Confirm box
	                bootbox.confirm("Are you sure want to delete?", function (result) {
	                  if (result) {
	                    // AJAX Request
	                    $.ajax({
	                      url: 'src/forms/delete_post.php?post_id=<?php echo $id; ?>',
	                      type: 'POST',
	                      data: {
	                        result: result
	                      },
	                      success: function success(response) {
	                        window.location.reload();
	                      }
	                    });
	                  }
	                });
	              });
	            });
	            </script>
	            <?php
				}

				if ($count > $limit) {
					$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
								<input type='hidden' class='noMorePosts' value='false'>";
				} else {
					$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'> No more posts to show! </p>";
				}
		  }
		    echo $str;
		  }

	////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function loadNewsPosts($data, $limit) 
    {
		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;


		$str = ""; //String to return
		$stmt = $this->spdo->prepare('SELECT * FROM news_posts WHERE category = ? ORDER BY id DESC');
		$stmt->execute(["news"]);

		$stmt2 = $this->spdo->query('SELECT COUNT(*) FROM news_posts WHERE category = "news" ORDER BY id DESC');
		$num_rows = $stmt2->fetchColumn();

        if ($num_rows > 0) 
        {
	        $num_iterations = 0;  // Number of results checked (not necessarily posted)
	        $count = 1;

            while($row = $stmt->fetch())  
            {
	        	$id = $row['id'];
				$title = $row['title'];
	          	$body = $row['body'];
	          	$added_by = $row['added_by'];
	          	$date_time = $row['date_added'];

				$user_logged_obj = new User($userLoggedIn, $this->spdo);
                if ( $user_logged_obj->isFriend($added_by) )
                {
					if($num_iterations++ < $start)
						continue;

                    // Once 10 posts have been loaded, break
                    if ($count > $limit) 
                    {
                        break;
                    }
                    else {
                        $count++;
                    }

                    // Delete Post Button
                    if ($userLoggedIn == $added_by)
                        $delete_button = "<button class='btn btn-danger close' id='post$id'><i class='typcn typcn-delete icon' style='font-size: 24px; margin: 5px;'></i></button>";
                    else
                        $delete_button = "";

                    $user_details_query = $this->spdo->prepare('SELECT displayname, avatar FROM users WHERE username = ?');
                    $user_details_query->execute([$added_by]);
                    $user_row = $user_details_query->fetch();
                    $displayname = $user_row['displayname'];
                    $avatar = $user_row['avatar'];

                    // Timeframe
                    $date_time_now = date("Y-m-d H:i:s");

                    $str .= "<div class='post'>
                                <a href='$added_by'>
                                <img src='$avatar' width='50' class=avatar title='$added_by'>
                                </a>
                                <div class='feed-post-header'><strong>$title</strong></div>
                                <div class='postTime'>posted $date_time by <a href='/$added_by'>$added_by</a></div>
                                <p class='card-text'>$body</p>
                            </div>";
		        }

			} //End while loop

			if ($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'> No more posts to show! </p>";
		}

		echo $str;

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getNumIOPosts()
    {
        $stmt = $this->spdo->prepare('SELECT * FROM news_posts WHERE category = ?');
        $stmt->execute(["io"]);
        $array = $stmt->rowCount();
        return $array;
    }
    
    public function getIOPostTitle($post_id)
    {
        $stmt = $this->spdo->prepare('SELECT title FROM news_posts WHERE category = ? AND post_id = ?');
        $stmt->execute(["io", $post_id]);
        $title = $stmt->fetchColumn();
        return $title;
    }

    public function getIOPostBody($post_id)
    {
        $stmt = $this->spdo->prepare('SELECT body FROM news_posts WHERE category = ? AND post_id = ?');
        $stmt->execute(["io", $post_id]);
        $body = $stmt->fetchColumn();
        return $body;
    }

    public function getIOPostAddedBy($post_id)
    {
        $stmt = $this->spdo->prepare('SELECT added_by FROM news_posts WHERE category = ? AND post_id = ?');
        $stmt->execute(["io", $post_id]);
        $added_by = $stmt->fetchColumn();
        return $added_by;
    }

    public function getIOPostDate($post_id)
    {
        $stmt = $this->spdo->prepare('SELECT date_added FROM news_posts WHERE category = ? AND post_id = ?');
        $stmt->execute(["io", $post_id]);
        $date_added = $stmt->fetchColumn();
        return $date_added;
    }

    public function getIOPostAddedByAvatar($post_id)
    {
        $stmt = $this->spdo->prepare('SELECT added_by FROM news_posts WHERE category = ? AND post_id = ?');
        $stmt->execute(["io", $post_id]);
        $added_by = $stmt->fetchColumn();
        $avi_obj = new User($added_by, $this->spdo);
        $avatar = $avi_obj->getAvatar();
        return $avatar;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    

    public function getTrends() 
    {
		$stmt = $this->spdo->prepare('SELECT * FROM trends ORDER BY hits DESC LIMIT 10');
		$stmt->execute();
		$row = $stmt->fetch();

        foreach ($stmt as $row) 
        {
			$word = $row['title'];
			$word_dot = strlen($word) >= 14 ? "..." : "";

			$trimmed_word = str_split($word, 14);
			$trimmed_word = $trimmed_word[0];
            
			echo "<li class='trending-list-group-item'>";
			echo $trimmed_word . $word_dot;
			echo "</li><br>";
            
		}
	}


    public function getSinglePost($post_id) 
    {
		$userLoggedIn = $this->user_obj->getUsername();

		$opened_query = $this->spdo->prepare('UPDATE notifications SET opened = ? WHERE user_to = ? AND link LIKE ? ');
		$opened_query->execute(["yes", $userLoggedIn, "%=$post_id"]);

		$str = ""; //String to return
		$data_query = $this->spdo->prepare('SELECT * FROM posts WHERE deleted = ? AND id = ?');
		$data_query->execute(["no", $post_id]);
		$num_rows_qry = $this->spdo->prepare('SELECT COUNT(*) FROM posts WHERE deleted = ? AND id = ?');
		$num_rows_qry->execute(["no", $post_id]);
		$num_rows = $num_rows_qry->fetchColumn();


        if ($num_rows > 0) 
        {
			$row = $data_query->fetch();
			$id = $row['id'];
			$body = $row['body'];
			$added_by = $row['added_by'];
			$date_time = $row['date_added'];
            $file_name = $row['file_name'];
            $file_type = $row['file_type'];

			//Prepare user_to string so  it can included even if not posted
			if($row['user_to'] == "none") {
				$user_to = "";
			}
			else {
				$user_to_obj = new User($row['user_to'], $this->spdo);
				$user_to_name = $user_to_obj->getDisplayName();
				$user_to = "to <a href=" . $row['user_to'] . ">" . $row['user_to'] . "</a>";
			}

			//Check if user who posted, has their account closed
			$added_by_obj = new User($added_by, $this->spdo);
			if($added_by_obj->isClosed()) {
				return;
			}

			$user_logged_obj = new User($userLoggedIn, $this->spdo);
			/* if($user_logged_obj->isFriend($added_by)){ */

	            // Delete Post Button
	          if($userLoggedIn == $added_by)
	            $delete_button = "<button class='btn btn-danger close' id='post$id'><i class='typcn typcn-delete icon' style='font-size: 24px; margin: 5px;'></i></button>";
	          else
	            $delete_button = "";

            $user_details_query = $this->spdo->prepare('SELECT displayname, avatar FROM users WHERE username = ?');
            $user_details_query->execute([$added_by]);
            $user_row = $user_details_query->fetch();
            $displayname = $user_row['displayname'];
            $avatar = $user_row['avatar'];

	          ?>

	          <script>

	          function toggle<?php echo $id; ?>() {

	             var element = document.getElementById("toggleComment<?php echo $id; ?>");

	             if(element.style.display == "block")
	                 element.style.display = "none";
	             else
	                 element.style.display = "block";
	          }

	          </script>

	          <?php

			//Check how many comments
		 	$comments_check = $this->spdo->prepare('SELECT COUNT(*) FROM comments WHERE post_id = ?');
		 	$comments_check->execute([$id]);
		 	$comments_check_num = $comments_check->fetchColumn();

			//Timeframe
			$time_message = $this->datetime($date_time);
            

            // new file upload routine 
            if ($file_type == "png" || $file_type == "jpeg" || $file_type == "jpg" || $file_type == "gif")
            {
                $file_insert = "<div class='card-img'><img src='$file_name' class='post_image'></div>";
            } 
            else if ($file_type == "mp4" || $file_type == "avi" || $file_type == "mkv") 
            {
                $file_insert = "<div class='w-100'><video class='post_image' controls>
                                    <source src='https://zeniea.com/$file_name'>
                                    Your browser does not support the video tag.
                                    </video> 
                                </div>";
            }
            else {
                $file_insert = "";
            }

			$str .= "<div class='status_post'>
								<div class='card'>
								<div class='card-body'>
									<a href='$added_by'>
									<img src='$avatar' width='50' class=avatar title='$added_by'>
									</a>
								<h4 class='card-title'>
										<a href='$added_by'>$displayname</a> $user_to
										<span class='postTime'>$time_message $delete_button</span>
								</h4>
								<p class='card-text'>$body<br>$file_insert</p>
								<br/>
								<div class='widgets'>
									<a onClick='javascript:toggle$id()' class='btn btn-primary card-link'><i class='typcn typcn-arrow-back icon'> Replies <span class='badge badge-primary'>$comments_check_num</span></i></a>
									<span class='likes'><iframe src='src/forms/like_form.php?post_id=$id' scrolling='no'></iframe></span>
								</div>
								</div>
								</div>
							</div>
							<p align='center'>
							<div class='post_comment' id='toggleComment$id' style='display:none;width: 100%; overflow:auto; height: auto;'>
								<iframe src='lib/pages/comment_frame.php?post_id=$id' id='comment_iframe' style='overflow:visible; height: 300px;' frameborder=0 width=100%></iframe>
							</div>
							</p>
							";
	            ?>

	            <script>
	            // Delete Post Modal Box
	            $(document).ready(function () {
	              $('#post<?php echo $id; ?>').on('click', function () {
	                // Confirm box
	                bootbox.confirm("Are you sure want to delete?", function (result) {
	                  if (result) {
	                    // AJAX Request
	                    $.ajax({
	                      url: 'src/forms/delete_post.php?post_id=<?php echo $id; ?>',
	                      type: 'POST',
	                      data: {
	                        result: result
	                      },
	                      success: function success(response) {
	                        window.location.reload();
	                      }
	                    });
	                  }
	                });
	              });
	            });
	            </script>
	            <?php
							/* } else {
								echo "<p>You cannot see this post you are not friends with this user.</p>";
								return;
						} */
	  			}
				else {
					echo "<p>No post found. If you clicked a link, it may be broken.</p>";
					return;
				}
	    echo $str;


	}

	private function datetime($date_time) {
		//post timeframe
		$date_time_now = date("Y-m-d H:i:s");
		$start_date = new DateTime($date_time); // Time of post
		$end_date = new DateTime($date_time_now); // current time

		$interval = $start_date->diff($end_date);
		if($interval->y >= 1) {
			if($interval->y == 1)
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
