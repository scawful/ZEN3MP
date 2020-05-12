<?php
namespace zen3mp;

class Inventory 
{
	private $user;
  	private $item_obj;
	private $spdo;
	private $rpdo;
	private $character;

    public function __construct($user, $character, $spdo, $rpdo) 
    {
		$this->rpdo = $rpdo;
		$this->spdo = $spdo;
		$this->user_obj = new User($user, $spdo);
		$this->character_obj = $character;

		$stmt = $this->rpdo->prepare('SELECT * FROM items WHERE active = ?');
		$stmt->execute(["yes"]);
		$this->all_items = $stmt->fetchAll();

		$this->user_id = $this->user_obj->getUserID();
	}

	public function getItemName($id) 
	{
		$id > 0 ? $id -= 1 : $id;
		return $this->all_items[$id]["name"];	
	}

	public function getItemDesc($id) {
		$id > 0 ? $id -= 1 : $id;
		return $this->all_items[$id]["desc"];	
	}

	public function getItemCost($id) {
		$id > 0 ? $id -= 1 : $id;
		return $this->all_items[$id]["price"];	
    }

    public function getNewestItemID() 
    {
        $stmt = $this->rpdo->prepare('SELECT id FROM items ORDER BY date_added DESC');
        $stmt->execute();
        $item = $stmt->fetchColumn();
        return $item;
    }

    public function getNumItems() 
    {
        $stmt = $this->rpdo->prepare('SELECT COUNT(*) FROM items WHERE active = ?');
        $stmt->execute(["yes"]);
        $num = $stmt->fetchColumn();
        return $num;
    }
    
    public function getCharacterInventoryAmount($item_id) 
    {
        $stmt = $this->rpdo->prepare('SELECT * FROM character_item WHERE character_id = ? AND item_id = ? LIMIT 1');
        $stmt->execute([$this->character_obj->getCharacterID(), $item_id]);
        $row = $stmt->fetch();
        $amount = $row['amount'];
        return $amount;
    }

    public function buyItem($item_id) 
    {
        $amount = $this->getCharacterInventoryAmount($item_id);

        $item_cost = $this->getItemCost($item_id);
        $character_gold = $this->character_obj->getCharacterMoney();

        if ($character_gold >= $item_cost) {

            if ($amount > 0) {
                $amount++;
                $stmt = $this->rpdo->prepare('UPDATE character_item SET amount = ? WHERE character_id = ? AND item_id = ?');
                $stmt->execute([$amount, $this->character_obj->getCharacterID(), $item_id]); 
            } else {
                $stmt = $this->rpdo->prepare('INSERT INTO character_item VALUES (0, ?, ?, ?)');
                $stmt->execute([$this->character_obj->getCharacterID(), $item_id, 1]);
            }
                    
            $updated_gold = $character_gold - $item_cost;
            $this->character_obj->setCharacterMoney($updated_gold);

        } else {
            echo "Not enough gold for purchase!";
        }
	}

    public function listItems($store_type) 
    {
		$store_items = $this->rpdo->prepare('SELECT * FROM items WHERE active = ? AND type_id = ?');
		$store_items->execute(["yes", $store_type]);
        $str = "";

	    while ($row = $store_items->fetch()) {
			$id = $row['id'];
			$type = $row['type_id'];
			$name = $row['name'];
			$desc = $row['desc'];
			$price = $row['price'];
            $equip_zone = $row['equip_zone'];
            $amount = $this->getCharacterInventoryAmount($id);
            $amount > 0 ? $amount : $amount = 0;

			$str .= "<tr>
						<th scope='row'><a href='?store&item=$id'>$name</a></th>
						<td>$desc</td>
                        <td>$price</td>
                        <td>$amount</td>
                        <td><a href='?store&buy=$id'>Buy</a></td>
					</tr>";
	    }
	    echo $str;

	}

    public function getInventoryValue() 
    {
		$stmt = $this->rpdo->query("SELECT SUM(price) AS value_inv FROM items");
		$sum = $stmt->fetchColumn();
		return $sum;
	}

    public function getitemInfo($item_id)
    {
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

		if ($icon == "N/A") {
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

    public function addNewItem($name, $type, $desc, $gold, $icon, $equip_zone, $lvl, 
                               $str, $int, $wpr, $agt, $spd, $end, $per, $wsd, $lck, $added_by)
    {
		$date_added = date("Y-m-d H:i:s");
		$new_item_query = $this->rpdo->prepare('INSERT INTO items VALUES (0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$new_item_query->execute([$type, $name, $desc, $icon, $gold, $lvl, $equip_zone, "yes", $date_added, $added_by]);

		$item_id = $this->rpdo->lastInsertId();
		$item_atrb_query = $this->rpdo->prepare('INSERT INTO item_attribute (item_id, attribute_id, value)
													VALUES (?, 1, ?), (?, 2, ?), (?, 3, ?), (?, 4, ?),
															(?, 5, ?),(?, 7, ?), (?, 8, ?), (?, 9, ?)');
        $item_atrb_query->execute([$item_id, $str, 
                                   $item_id, $int, 
                                   $item_id, $wpr, 
                                   $item_id, $agt, 
                                   $item_id, $spd, 
                                   $item_id, $end, 
                                   $item_id, $per, 
                                   $item_id, $wsd, 
                                   $item_id, $lck]);

	}

    public function getPlayerInventory() 
    {
		$user_id = $this->user_obj->getUserID();
		$user_character = $this->rpdo->query("SELECT * FROM user_character WHERE user_id='$user_id'");
		$row = $user_character->fetch();
		$character_id = $row['character_id'];
		$character_item_query = $this->rpdo->query("SELECT * FROM character_item WHERE character_id='$character_id'");
		$str = "";

        while ($row2 = $character_item_query->fetch()) 
        {
            $item_id = $row2['item_id'];
            $amount = $row2['amount'];

			$item_info_query = $this->rpdo->query("SELECT * FROM items WHERE id='$item_id'");
            $row3 = $item_info_query->fetch();
			$name = $row3['name'];
			$desc = $row3['desc'];
			$price = $row3['price'];
			$equip_zone = $row3['equip_zone'];
			$lvl = $row3['required_level'];
			$icon = $row3['icon'];

            $str .= "<tr>
                    <th scope='row'>$name</th>
                    <td>$desc</td>
                    <td>$amount</td>
                    <td>Equip</td>
                    </tr>";
		}
		echo $str;
	}


}
?>