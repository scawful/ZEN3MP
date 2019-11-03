<?php
require "../config.php";

	if(isset($_GET['post_id']))
		$post_id = $_GET['post_id'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true')
			$query = mysqli_query($connect_social, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
	}

?>
