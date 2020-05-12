<?php
namespace zen3mp;

class Character 
{
	private $user_obj;
	private $spdo;
	private $rpdo;

	public function __construct($user, $spdo, $rpdo) 
	{
		$this->user_obj = new User($user, $spdo);
		$this->spdo = $spdo;
        $this->rpdo = $rpdo;
        $this->user = $user;

		$this->user_id = $this->user_obj->getUserID();
		
		$stmt = $this->rpdo->prepare('SELECT character_id FROM user_character WHERE user_id = ? LIMIT 1');
		$stmt->execute([$this->user_id]);
		$user_character_id_array = $stmt->fetch();

	    $user_character_id = $user_character_id_array['character_id'];

		$char_details = $this->rpdo->prepare('SELECT * FROM rpg_character WHERE character_id = ?');
		$char_details->execute([$user_character_id]);
        $this->character = $char_details->fetch();

        $char_atrb = $this->rpdo->prepare('SELECT * FROM character_attribute WHERE character_id = ?');
        $char_atrb->execute([$user_character_id]);
        $this->character_atrb = $char_atrb->fetch();
	}

	public function getCharacterID() {
		return $this->character['character_id'];
	}

	public function getCharacterHealth() {
		return $this->character['health'];
	}

	public function getCharacterMana() {
		return $this->character['mana'];
	}

	public function getCharacterFatigue() {
		return $this->character['fatigue'];
	}

	public function getCharacterName() {
		return $this->character['name'];
	}

	public function getCharacterMoney() {
		return $this->character['money'];
	}

	public function getCharacterRace() {
		return $this->character['race'];
	}

	public function getCharacterLevel() {
		return $this->character['level'];
	}

	public function getCharacterClass() {
		return $this->character['class'];
    }

    public function getCharacterExp(){
        return $this->character['xp'];
    }

    public function getCharacterPrevExp(){
        return $this->character['prev_xp'];
    }

    public function getCharacterLuck() {
        return $this->character_atrb['attribute_id'][9];
    }

    public function setCharacterLevel($lvl)
    {
        $stmt = $this->rpdo->prepare('UPDATE rpg_character SET level = ? WHERE character_id = ?');
        $stmt->execute([$lvl, $this->getCharacterID()]);
    }

    public function setCharacterMoney($amount) 
    {
        $stmt = $this->rpdo->prepare('UPDATE rpg_character SET money = money + ? WHERE character_id = ?');
        $stmt->execute([$amount, $this->getCharacterID()]);
    }

    public function setCharacterExp($amount)
    {
        $stmt = $this->rpdo->prepare('UPDATE rpg_character SET xp = xp + ? WHERE character_id = ?');
        $stmt->execute([$amount, $this->getCharacterID()]);
    }

    public function setCharacterPrevExp($amount)
    {
        $stmt = $this->rpdo->prepare('UPDATE rpg_character SET prev_xp = ? WHERE character_id = ?');
        $stmt->execute([$amount, $this->getCharacterID()]);
    }

    public function checkForLevel()
    {  
        $currentLevel = $this->getCharacterLevel();
        $prevExp = $this->getCharacterPrevExp();
        $modifier = $prevExp + ($prevExp * 0.10);
        $added_by = $this->user_obj->getUsername();

        if ( $this->getCharacterExp() > $modifier )
        {
            $currentLevel++;
            $this->setCharacterPrevExp($modifier);
            $this->setCharacterLevel($currentLevel);
            $notification = new Notification($this->user, $this->spdo, $this->rpdo);
            $notification->insertRPGNotification(0, $added_by, "level_up", $currentLevel);
        }
    }

    public function checkForCharacter() 
    {
        $stmt = $this->rpdo->prepare('SELECT 1 FROM user_character WHERE user_id = ? LIMIT 1');
        $stmt->execute([$this->user_id]);
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getAllCharacterMoney() 
    {
		$stmt = $this->rpdo->prepare('SELECT SUM(money) AS sum_money FROM rpg_character');
		$stmt->execute();
		$row = $stmt->fetch();
		$sum = $row['sum_money'];
		return $sum;
    }

    public function listCharacterAttributes() 
    {
		$user_character_id = $this->getCharacterID();
		$stmt = $this->rpdo->prepare('SELECT * FROM character_attribute WHERE character_id = ?');
		$stmt->execute([$user_character_id]);
		echo "<div style='float:left;'>Strength<br>Intelligence<br>Willpower<br>Agility<br>Speed<br>Endurance<br>Personality<br>Wisdom<br>Luck</div>";
        while ($row = $stmt->fetch())
        {
	        printf ("<div style='float: right;'> %d</div><br>", $row['value']);
		}
	}
}
?>
