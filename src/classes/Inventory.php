<?php
namespace zen3mp;

class Inventory {

	private $user_obj;
  	private $item_obj;
	private $spdo;
	private $rpdo;

	public function __construct($user_obj, $spdo, $rpdo) {
		$this->rpdo = $rpdo;
		$this->spdo = $spdo;
		$this->user_obj = new User($user_obj, $spdo);

		$stmt = $this->rpdo->prepare('SELECT * FROM items WHERE active = ?');
		$stmt->execute(["yes"]);
		$this->all_items = $stmt->fetch();

		$userLoggedIn = $this->user_obj->getUsername();
		$user_id = $this->user_obj->getUserID();

	}

	public function listItems($store_type){

		$store_items = $this->rpdo->prepare('SELECT * FROM items WHERE active = ? AND type_id = ?');
		$store_items->execute(["yes", $store_type]);
	    $str = "";

	    while($row = $store_items->fetch()) {
	      $id = $row['id'];
	      $type = $row['type_id'];
	      $name = $row['name'];
	      $desc = $row['desc'];
	      $price = $row['price'];
	      $equip_zone = $row['equip_zone'];

	      $str .= "<tr>
	                <th scope='row'><a href='?store&item=$id'>$name</a></th>
	                <td>$desc</td>
	                <td>$price</td>
	                <td><a href='?buy=$id'>Buy</a> | <a href='?sell=$id'>Sell</a></td>
	              </tr>";
	    }
	    echo $str;

	}

	public function getInventoryValue(){
		$stmt = $this->rpdo->query("SELECT SUM(price) AS value_inv FROM items");
		$sum = $stmt->fetchColumn();
		echo "Total Stores Value: " . $sum . "<br />";
	}

	public function getitemInfo($item_id){
		$item_query = $this->rpdo->query("SELECT * FROM items WHERE id='$item_id'");
		$row = $item_query->fetch();

		$item_stats_query = $this->rpdo->query("SELECT * FROM item_attribute WHERE id='$item_id'");
		$row2 = $item_stats_query->fetch();

	    $str = "";
	    $type = $row['type_id'];
	    $name = $row['name'];
	    $desc = $row['desc'];
	    $price = $row['price'];
	    $equip_zone = $row['equip_zone'];
		$lvl = $row['required_level'];
		$icon = $row['icon'];

		if($icon == "N/A"){
			$icon_div = "";
		} else {
			$icon_div = "<img class='card-img-top' src='$icon'>";
		}

	    $str .= "<div class='card'>
								<div class='card-header'>$name</div>
									$icon_div
									<div class='card-body'>Price: $price<br />
									Required Level: $lvl<br />
									Description: $desc <br />
								</div>
							</div>";
	    echo $str;


  }

	public function addNewItem($name, $type, $desc, $gold, $icon, $equip_zone, $lvl, $str, $int, $wpr, $agt, $spd, $end, $per, $wsd, $lck, $added_by){

		$date_added = date("Y-m-d H:i:s");
		$new_item_query = $this->rpdo->prepare('INSERT INTO items VALUES (0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$new_item_query->execute([$type, $name, $desc, $icon, $gold, $lvl, $equip_zone, "yes", $date_added, $added_by]);
		$item_id = $this->rpdo->lastInsertId();
		$item_atrb_query = $this->rpdo->prepare('INSERT INTO item_attribute (item_id, attribute_id, value)
													VALUES (?, 1, ?), (?, 2, ?), (?, 3, ?), (?, 4, ?),
														(?, 5, ?),(?, 7, ?), (?, 8, ?), (?, 9, ?)');
		$item_atrb_query->execute([$item_id, $str, $item_id, $int, $item_id, $wpr, $item_id, $agt, $item_id, $spd, $item_id, $end, $item_id, $per, $item_id, $wsd, $item_id, $lck]);

	}

	public function getPlayerInventory() {
		$user_id = $this->user_obj->getUserID();
		$user_character = $this->rpdo->query("SELECT * FROM user_character WHERE user_id='$user_id'");
		$row = $user_character->fetch();
		$character_id = $row['character_id'];
		$character_item_query = $this->rpdo->query("SELECT * FROM character_item WHERE character_id='$character_id'");
		$str = "";

		while($row2 = $character_item_query->fetch()) {
			$item_id = $row2['item_id'];
			$item_info_query = $this->rpdo->query("SELECT * FROM items WHERE id='$item_id'");
			$row3 = $item_info_query->fetch();
			$name = $row3['name'];
			$desc = $row3['desc'];
			$price = $row3['price'];
			$equip_zone = $row3['equip_zone'];
			$lvl = $row3['required_level'];
			$icon = $row3['icon'];

			$str .= "<div class='card' style='width: 25%; margin: 5px;'>
								<div class='card-header'>$name</div>
								<div class='card-body'>$desc</div>

							</div>
							";
			//$player_chara = mysqli_query($this->rpgcon, "SELECT * FROM rpg_character WHERE character_id='$character_id'");

		}
		echo "<div style='display: flex; flex-direction: row;'>";
		echo $str;
		echo "</div>";

	}


}



?>
