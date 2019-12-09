<?php
require "../config.php";

	if(isset($_GET['post_id']))
		$post_id = $_GET['post_id'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true') {
			$stmt = $spdo->prepare('UPDATE posts SET deleted = ? WHERE id = ?');
			$stmt->execute(["yes", $post_id]);
		}
	}

?>
