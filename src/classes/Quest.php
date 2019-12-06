<?php
namespace zen3mp;

class Quest {

	private $user_obj;
	private $rpdo;
	private $spdo;

	public function __construct($user_obj, $rpdo, $spdo) {
		$this->rpdo = $rpdo;
		$this->spdo = $spdo;
		$this->user = new User($user_obj, $spdo);
	}

////////////////////////////////////////////////////////////////////////////////////////////

	public function listQuests($category){

		$stmt = $this->rpdo->query("SELECT * FROM quests WHERE active='yes' AND category='$category'");
		$str = "";

		while($row = $stmt->fetch()) {
			$id = $row['id'];
			$title = $row['title'];
			$desc = $row['desc'];
			$lvl = $row['lvreq'];

			$str .= "<h5>$title
			        <div class='float-right'>
			          <a href='?world&q=$id' class='btn btn-primary'>Begin Quest</a>
			        </div>
			      </h5>
			      <small>Level Required: $lvl <br> $desc  </small>
			      <hr>";
		}
		echo $str;

	}

	public function listQuestsAdmin() {

		


	}


////////////////////////////////////////////////////////////////////////////////////////////

	public function showQuestHome($quest_id) { // load first page of a quest

		$stmt = $this->rpdo->prepare('SELECT * FROM quest_pages WHERE q_id = ?');
		$stmt->execute([$quest_id]);
		$quest = $stmt->fetch();

		$id = $quest['id'];
		$title = $quest['title'];
		$body = $quest['body'];
		$media = $quest['media'];
		$next_page = $quest['p_id'] + 1;

		echo "<div class='card'>
		        <div class='card-header'>$title</div>
		        <img class='card-img-top' src='$media'>
		        <div class='card-body'>
		          <p class='card-text'>$body</p>
				  	<div class='float-left'>
	  	            <a href='?world' class='btn btn-primary'>
	  					<i class='typcn typcn-arrow-left-thick icon'></i> Return to Worlds
	  				</a>
	  	            </div>
		            <div class='float-right'>
		            <a href='?world&q=$quest_id&p=$next_page' class='btn btn-primary'>Begin Quest
						<i class='typcn typcn-arrow-right-thick icon'></i></a>
		            </div>
		        </div>
		      </div>
		      ";
	}

////////////////////////////////////////////////////////////////////////////////////////////

  public function loadPage($page_id) {

    $stmt = $this->rpdo->prepare('SELECT * FROM quest_pages WHERE p_id = ?');
    $stmt->execute([$page_id]);
    $page = $stmt->fetch();

    $title = $page['title'];
    $body = $page['body'];
    $media = $page['media'];
	$quest = $page['q_id'];
	$prevpage = $page_id - 1;
	$nextpage = $page_id + 1;

	if($page_id == 1) {
		$leftmsg = "Return Home";
	} else {
		$leftmsg = "Previous Page";
	}

    echo "<div class='card'>
            <div class='card-header'>$title</div>
            <img class='card-img-top' src='$media'>
            <div class='card-body'>
              	<p class='card-text'>$body</p>
				<div class='d-flex justify-content-between d-inline'>
	            <div class='float-left'>
	            <a href='?world&q=$quest&p=$prevpage' class='btn btn-primary'>
					<i class='typcn typcn-arrow-left-thick icon'></i> $leftmsg
				</a>
	            </div>

					<a href='' class='btn btn-primary'>
						<i class='typcn typcn-bookmark icon'></i> Save / Load
					</a>
					<a href='' class='btn btn-primary'>
						<i class='typcn typcn-archive icon'></i> Logs
					</a>
					<a href='' class='btn btn-primary'>
						<i class='typcn typcn-document-text icon'></i> Author's Note
					</a>

				<div class='float-right'>
				<a href='?world&q=$quest&p=$nextpage' class='btn btn-primary'>
					Next Page <i class='typcn typcn-arrow-right-thick icon'></i>
				</a>
				</div>
				</div>
            </div>
          </div>
          ";
  }

////////////////////////////////////////////////////////////////////////////////////////////

	public function battleMenu() {
		echo "<div class='btn-group' role='group' aria-label='Commands'>
		        <button type='button' class='btn btn-warning'>Fight</button>
		        <button type='button' class='btn btn-info'>Mana</button>
		        <button type='button' class='btn btn-dark'>Talk</button>
		        <button type='button' class='btn btn-success'>Bag</button>
		        <button type='button' class='btn btn-danger'>Run</button>
		     </div>";
	}

////////////////////////////////////////////////////////////////////////////////////////////



}
?>
