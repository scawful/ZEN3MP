<?php
namespace zen3mp;
require "../config.php";

	if(isset($_GET['post_id']))
		$post_id = $_GET['post_id'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true') {
			$stmt = $spdo->prepare('UPDATE posts SET deleted = ? WHERE id = ?');
            $stmt->execute(["yes", $post_id]);

            $user = $spdo->prepare('SELECT added_by FROM posts WHERE id = ?');
            $user->execute([$post_id]);
            $username = $user->fetchColumn();

            $stmt2 = $spdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt2->execute([$username]);
            $user_id = $stmt2->fetchColumn();

            $stmt3 = $spdo->prepare('UPDATE users SET num_posts = num_posts - 1 WHERE id = ?');
            $stmt3->execute([$user_id]);
		}
	}

?>
