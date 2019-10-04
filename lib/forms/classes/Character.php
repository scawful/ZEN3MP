<?php
class Character {
	private $user_obj;
	private $connect_social;
  	private $connect_rpg;
	private $spdo;

	public function __construct($user, $spdo, $rpdo){
		$this->user_obj = new User($user, $spdo);
		$this->spdo = $spdo;
		$this->rpdo = $rpdo;

	    $user_id = $this->user_obj->getUserID();
	    //$activeCharId = mysqli_query($connect_rpg, "SELECT character_id FROM user_character WHERE user_id='$user_id' LIMIT 1");
		$stmt = $this->rpdo->prepare('SELECT character_id FROM user_character WHERE user_id = ? LIMIT 1');
		$stmt->execute([$user_id]);
		$user_character_id_array = $stmt->fetch();
	    //$user_character_id_array = mysqli_fetch_array($activeCharId);
	    $user_character_id = $user_character_id_array['character_id'];

	    //$char_details_query = mysqli_query($connect_rpg, "SELECT * FROM rpg_character WHERE character_id='$user_character_id'");
		$char_details_query = $this->rpdo->prepare('SELECT * FROM rpg_character WHERE character_id = ?');
		$char_details_query->execute([$user_character_id]);
		$this->character = $char_details_query->fetch();
	    //$this->character = mysqli_fetch_array($char_details_query);
	}

	public function dispCharacterStats(){

		$charHealthBar = "<div class='progress' style='margin-bottom: 5px; width: 100%;'>
							<div class='progress-bar bg-success' role='progressbar' style='width: 100%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'>Health: " . $this->getCharacterHealth() . "</div>
							</div>";
		$charManaBar = "<div class='progress' style='margin-bottom: 5px; width: 100%;'>
							<div class='progress-bar bg-info' role='progressbar' style='width: 100%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'>Mana: " . $this->getCharacterMana() . "</div>
						</div>";
		$charFatigueBar = "<div class='progress' style='margin-bottom: 5px; width: 100%;'>
							<div class='progress-bar bg-warning' role='progressbar' style='width: 100%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'>Fatigue: " . $this->getCharacterFatigue() . "</div>
						   </div>";
		echo "<div class='article'>";
		echo $charHealthBar;
    	echo $charManaBar;
    	echo $charFatigueBar . "</div>";
	}

	public function getCharacterID(){
		return $this->character['character_id'];
	}

	public function getCharacterHealth(){
		return $this->character['health'];
	}

	public function getCharacterMana(){
		return $this->character['mana'];
	}

	public function getCharacterFatigue(){
		return $this->character['fatigue'];
	}

	public function getCharacterName(){
		return $this->character['name'];
	}

	public function getCharacterMoney(){
		return $this->character['money'];
	}

	public function getCharacterRace(){
		return $this->character['race'];
	}

	public function getCharacterLevel(){
		return $this->character['level'];
	}

	public function getCharacterClass(){
		return $this->character['class'];
	}

	public function listCharacterAttributes(){
		$user_character_id = $this->getCharacterID();
		$stmt = $this->rpdo->prepare('SELECT * FROM character_attribute WHERE character_id = ?');
		$stmt->execute([$user_character_id]);
		echo "<div style='float:left;'>Strength<br>Intelligence<br>Willpower<br>Agility<br>Speed<br>Endurance<br>Personality<br>Wisdom<br>Luck</div>";
		while($row = $stmt->fetch()) {
		  printf ("<div style='float: right;'> %d</div><br>", $row['value']);
		}
	}

	public function getAllCharacterMoney(){
		$stmt = $this->rpdo->prepare('SELECT SUM(money) AS sum_money FROM rpg_character');
		$stmt->execute();
		$row = $stmt->fetch();
		$sum = $row['sum_money'];
		echo "Total Character Gold: " . $sum . "<br />";
	}



}
?>
