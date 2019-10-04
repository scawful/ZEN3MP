<?php
class Inventory {

	private $user_obj;
  private $item_obj;
	private $connect_social;
  private $connect_rpg;
	private $spdo;

  public function __construct($connect_social, $connect_rpg, $user_obj, $spdo){
    $this->con = $connect_social;
    $this->rpgcon = $connect_rpg;
    $this->user_obj = new User($user_obj, $spdo);

    $all_items_query = mysqli_query($this->rpgcon, "SELECT * FROM items WHERE active='yes'");
    $this->inventory = mysqli_fetch_array($all_items_query);

    $userLoggedIn = $this->user_obj->getUsername();
    $user_id = $this->user_obj->getUserID();

  }

  public function listItems($store_type){

    $store_items_query = mysqli_query($this->rpgcon, "SELECT * FROM items WHERE active='yes' AND type_id='$store_type'");
    $str = "";

    while($row = mysqli_fetch_array($store_items_query)) {
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
    $inventory_query = mysqli_query($this->rpgcon, "SELECT SUM(price) AS value_inv FROM items");
    $row = mysqli_fetch_array($inventory_query);
    $sum = $row['value_inv'];
    echo "Total Stores Value: " . $sum . "<br />";
  }

  public function getitemInfo($item_id){
    $item_query = mysqli_query($this->rpgcon, "SELECT * FROM items WHERE id='$item_id'");
    $row = mysqli_fetch_array($item_query);
		$item_stats_query = mysqli_query($this->rpgcon, "SELECT * FROM item_attribute WHERE id='$item_id'");
		$row2 = mysqli_fetch_array($item_stats_query);

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
								<div class='card-body'>Price: $price<br /> Required Level: $lvl<br /> Description: $desc <br />

							</div>
						</div>
            ";
    echo $str;


  }

	public function addNewItem($name, $type, $desc, $gold, $icon, $equip_zone, $lvl, $str, $int, $wpr, $agt, $spd, $end, $per, $wsd, $lck, $added_by){

		//Current date and time
		$date_added = date("Y-m-d H:i:s");

		$new_item_query = mysqli_query($this->rpgcon, "INSERT INTO items
																									 VALUES(0, '$type', '$name', '$desc', '$icon', '$gold', '$lvl', '$equip_zone', 'yes', '$date_added', '$added_by')");
		$item_id = mysqli_insert_id($this->rpgcon);
		$item_attribute_query = mysqli_query($this->rpgcon, "INSERT INTO item_attribute (item_id, attribute_id, value)
																												VALUES ($item_id, 1, $str),
																												($item_id, 2, $int),
																												($item_id, 3, $wpr),
																												($item_id, 4, $agt),
																												($item_id, 5, $spd),
																												($item_id, 6, $end),
																												($item_id, 7, $per),
																												($item_id, 8, $wsd),
																												($item_id, 9, $lck)");
	}

	public function getPlayerInventory() {
		$user_id = $this->user_obj->getUserID();
		$user_character = mysqli_query($this->rpgcon, "SELECT * FROM user_character WHERE user_id='$user_id'");
		$row = mysqli_fetch_array($user_character);
		$character_id = $row['character_id'];
		$character_item_query = mysqli_query($this->rpgcon, "SELECT * FROM character_item WHERE character_id='$character_id'");
		$str = "";

		while($row2 = mysqli_fetch_array($character_item_query)) {
			$item_id = $row2['item_id'];
			$item_info_query = mysqli_query($this->rpgcon, "SELECT * FROM items WHERE id='$item_id'");
			$row3 = mysqli_fetch_array($item_info_query);
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
