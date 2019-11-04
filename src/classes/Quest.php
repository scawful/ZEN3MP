<?php
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
                <a href='?world&q=$quest_id&p=$next_page' class='btn btn-primary'>Begin Quest <i class='typcn typcn-arrow-right-thick icon'></i></a>
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

    echo "<div class='card'>
            <div class='card-header'>$title</div>
            <img class='card-img-top' src='$media'>
            <div class='card-body'>
              	<p class='card-text'>$body</p>
	            <div class='float-left'>
	            <a href='#' class='btn btn-primary'>Put Commands Here <i class='typcn typcn-arrow-right-thick icon'></i></a>
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


}
?>
