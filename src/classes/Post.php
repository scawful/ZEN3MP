<?php
namespace zen3mp;
use \Datetime;

class Post {
		private $user_obj;
		private $connect_social;
		private $spdo;

	public function __construct($connect_social, $user, $spdo){
			$this->con = $connect_social;
			$this->user_obj = new User($user, $spdo);
			$this->spdo = $spdo;
	}

	public function submitPhoto($user_to, $imageName) {

		//Current date and time
		$date_added = date("Y-m-d H:i:s");
		//Get username
		$added_by = $this->user_obj->getUsername();

		//If user is on own profile, user_to is 'none'
		if($user_to == $added_by) {
			$user_to = "none";
		}

		$stmt = $this->spdo->prepare('INSERT INTO posts VALUES(0, ?, ?, ?, ?, ?, ?, ?, ?)');
		$stmt->execute([" ", $added_by, $user_to, $date_added, "no", "no", "0", $imageName]);
		$returned_id = $this->spdo->lastInsertId();

		//$query = mysqli_query($this->con, "INSERT INTO posts VALUES(0, ' ', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imageName')");
		//$returned_id = mysqli_insert_id($this->con);

		//Insert notification
		if($user_to != 'none') {
				$notification = new Notification($added_by, $spdo);
				$notification->insertNotification($returned_id, $user_to, "profile_post");
		}

		//Update post count for user
		$num_posts = $this->user_obj->getNumPosts();
		$num_posts++;
		$update_query = $this->spdo->prepare('UPDATE users SET num_posts = ? WHERE username = ?');
		$update_query->execute([$num_posts, $added_by]);
		//$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

	}

	public function submitNewsPost($title, $body, $added_by) {

		//Current date and time
		$date_added = date("Y-m-d H:i:s");

		$stmt = $this->spdo->prepare('INSERT INTO news_posts VALUES(0, ?, ?, ?, ?, ?)');
		$stmt->execute([$title, $body, $added_by, $date_added, "0"]);
		//$query = mysqli_query($this->con, "INSERT INTO news_posts VALUES(0, '$title', '$body', '$added_by', '$date_added', '0')");
		//$returned_id = mysqli_insert_id($this->con);

	}

	public function submitPost($body, $user_to, $imageName) {
			$body = strip_tags($body); //removes html tags
			$body = mysqli_real_escape_string($this->con, $body);
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

				//If user is on own profile, user_to is 'none'
				if($user_to == $added_by) {
					$user_to = "none";
				}

				//insert post
				$stmt = $this->spdo->prepare('INSERT INTO posts VALUES(0, ?, ?, ?, ?, ?, ?, ?, ? )');
				$stmt->execute([$body, $added_by, $user_to, $date_added, "no", "no", "0", $imageName]);
				$returned_id = $this->spdo->lastInsertId();
				///$query = mysqli_query($this->con, "INSERT INTO posts VALUES(0, '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imageName')");
				///$returned_id = mysqli_insert_id($this->con);

				//Insert notification
				if($user_to != 'none') {
						$notification = new Notification($added_by, $spdo);
						$notification->insertNotification($returned_id, $user_to, "profile_post");
				}

				//Update post count for user
				$num_posts = $this->user_obj->getNumPosts();
				$num_posts++;
				$update_query = $this->spdo->prepare('UPDATE users SET num_posts = ? WHERE username = ?');
				$update_query->execute([$num_posts, $added_by]);
				///$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

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
				hate sleepy reason for some little yes bye choose nigger penis fuck shit dick piss bitch";

				//Convert stop words into array - split at white space
				$stopWords = preg_split("/[\s,]+/", $stopWords);

				//Remove all punctionation
				$no_punctuation = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);

				//Predict whether user is posting a url. If so, do not check for trending words
				if(strpos($no_punctuation, "height") === false && strpos($no_punctuation, "width") === false
					&& strpos($no_punctuation, "http") === false && strpos($no_punctuation, "youtube") === false){
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

	public function calculateTrend($term) {

			if($term != '') {
				$stmt = $this->spdo->prepare('SELECT * FROM trends WHERE title = ?');
				$stmt->execute([$term]);
				$num_rows = $stmt->fetchColumn();
				//$query = mysqli_query($this->con, "SELECT * FROM trends WHERE title='$term'");

				//if(mysqli_num_rows($query) == 0) {
				if($num_rows == 0) {
					$insert_query = $this->spdo->prepare('INSERT INTO trends(title,hits) VALUES(?, ?)');
					$insert_query->execute([$term, '1']);
					///$insert_query = mysqli_query($this->con, "INSERT INTO trends(title,hits) VALUES('$term','1')");
				} else {
					$insert_query = $this->spdo->prepare('UPDATE trends SET hits = ? WHERE title = ?');
					$insert_query->execute([hits+1, $term]);
					///$insert_query = mysqli_query($this->con, "UPDATE trends SET hits=hits+1 WHERE title='$term'");
				}
		}

	}

	public function loadPostsFriends($data, $limit) {

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
		  ///$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

	    if($num_rows > 0) { ///mysqli_num_rows($data_query)

	        $num_iterations = 0;  //Number of results checked (not necessaryily posted)
	        $count = 1;


	        while($row = $data_query->fetch())  {
	          $id = $row['id'];
	          $body = $row['body'];
	          $added_by = $row['added_by'];
	          $date_time = $row['date_added'];
						$imagePath = $row['image'];

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

						if($imagePath != "") {
							$imageDiv = "<div class='card-img'>
															<img src='$imagePath' class='post_image'>
														</div>";
						}
						else {
							$imageDiv = "";
						}

						$str .= "<div class='status_post'>
											<div class='card'>
											<div class='card-body'>
												<a href='$added_by'>
												<img src='$avatar' width='50' class=avatar title='$added_by'>
												</a>
											<h4 class='card-title'>
													<a href='$added_by'>$displayname</a> $user_to
													<span class='postTime'><a href='post.php?id=$id'>$time_message</a> $delete_button</span>
											</h4>
											<p class='card-text'>$body<br>$imageDiv</p>
											<br/>
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
											<iframe src='lib/pages/comment_frame.php?post_id=$id' id='comment_iframe' style='overflow:visible; height: 200px;' frameborder=0 width=100%></iframe>
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

	public function loadProfilePosts($data, $limit) {
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
	    ///$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser') ORDER BY id DESC");

	    if($num_rows > 0) {

	        $num_iterations = 0;  //Number of results checked (not necessaryily posted)
	        $count = 1;


	        while($row = $data_query->fetch())  { ///mysqli_fetch_array($data_query)
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$imagePath = $row['image'];

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
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($row['user_to'], $this->spdo);
					$user_to_name = $user_to_obj->getDisplayName();
					$user_to = "to <a href=" . $row['user_to'] . ">" . $row['user_to'] . "</a>";
				}

				// Delete Post Button
				if($userLoggedIn == $added_by)
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



				if($imagePath != "") {
					$imageDiv = "<div class='card-img'>
									<img src='$imagePath' class='post_image'>
								</div>";
				}
				else {
					$imageDiv = "";
				}

	          $str .= "<div class='status_post'>
	                    <div class='card'>
	                    <div class='card-body'>
	                      <a href='$added_by'>
	                      <img src='$avatar' width='50' class=avatar title='$added_by'>
	                      </a>
	                    <h4 class='card-title'>
	                        <a href='$added_by'>$displayname</a> $user_to
	                        <span class='postTime'><a href='post.php?id=$id'>$time_message</a> $delete_button</span>
	                    </h4>
	                    <p class='card-text'>$body<br>$imageDiv</p>
											<br/>
	                    <div class='widgets'>
	                      <a onClick='javascript:toggle$id()' class='btn btn-primary card-link'><i class='typcn typcn-arrow-back icon'> Replies <span class='badge badge-primary'>$comments_check_num</span></i></a>
	                      <span class='likes'><iframe src='src/forms/like_form.php?post_id=$id' scrolling='no'></iframe></span>
	                    </div>
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

				if($count > $limit) {
					$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
								<input type='hidden' class='noMorePosts' value='false'>";
				} else {
					$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'> No more posts to show! </p>";
				}
		  }
		    echo $str;
		  }

	////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function loadNewsPosts($data, $limit) {

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;


		$str = ""; //String to return
		$stmt = $this->spdo->prepare('SELECT * FROM news_posts ORDER BY id DESC');
		$stmt->execute();

		$stmt2 = $this->spdo->query('SELECT COUNT(*) FROM news_posts ORDER BY id DESC');
		$num_rows = $stmt->fetchColumn();

	    if($num_rows > 0) {

	        $num_iterations = 0;  //Number of results checked (not necessaryily posted)
	        $count = 1;


	        while($row = $stmt->fetch())  {
	        	$id = $row['id'];
				$title = $row['title'];
	          	$body = $row['body'];
	          	$added_by = $row['added_by'];
	          	$date_time = $row['date_added'];


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

	          	//Timeframe
	        	$date_time_now = date("Y-m-d H:i:s");
	        	$start_date = new DateTime($date_time); // Time of post
	        	$end_date = new DateTime($date_time_now); // current time


				$str .= "<div class='post'>
										<a href='$added_by'>
										<img src='$avatar' width='50' class=avatar title='$added_by'>
										</a>
										<div class='feed-post-header'><strong>$title</strong></div>
											<div class='postTime'>posted $date_time by <a href='/$added_by'>$added_by</a></div>
									<p class='card-text'>$body</p>
								</div>
								";

		            }

			} //End while loop

			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'> No more posts to show! </p>";
		}

		echo $str;

	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function getSinglePost($post_id) {
			$userLoggedIn = $this->user_obj->getUsername();

			$opened_query = $this->spdo->prepare('UPDATE notifications SET opened = ? WHERE user_to = ? AND link LIKE ? ');
			$opened_query->execute(["yes", $userLoggedIn, "%=$post_id"]);

			///$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

			$str = ""; //String to return
			$data_query = $this->spdo->prepare('SELECT * FROM posts WHERE deleted = ? AND id = ?');
			$data_query->execute(["no", $post_id]);
			$num_rows_qry = $this->spdo->prepare('SELECT COUNT(*) FROM posts WHERE deleted = ? AND id = ?');
			$num_rows_qry->execute(["no", $post_id]);
			$num_rows = $num_rows_qry->fetchColumn();

			///$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");

	    if($num_rows > 0) {

	        	$row = $data_query->fetch();
	          $id = $row['id'];
	          $body = $row['body'];
	          $added_by = $row['added_by'];
	          $date_time = $row['date_added'];
						$imagePath = $row['image'];


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

						if($imagePath != "") {
							$imageDiv = "<div class='card-img'>
															<img src='$imagePath' class='post_image'>
														</div>";
						}
						else {
							$imageDiv = "";
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
											<p class='card-text'>$body<br>$imageDiv</p>
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
				if($interval == 1)
					$time_message = $interval->y . " year ago"; // 1 year ago
				else
					$time_message = $interval->y . " years ago"; // 1+ year ago
			}
			else if ($interval-> m >= 1) {
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
