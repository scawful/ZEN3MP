<?php
namespace zen3mp;
// Stat variables
$healthStat = $glob_base_char_hp;
$manaStat = $glob_base_char_mana;
$fatigueStat = $glob_base_char_ftg;
$strengthAtrb = $glob_base_char_str;
$intelligenceAtrb = $glob_base_char_int;
$willpowerAtrb = $glob_base_char_wpr;
$agilityAtrb = $glob_base_char_agt;
$speedAtrb = $glob_base_char_spd;
$enduranceAtrb = $glob_base_char_end;
$personalityAtrb = $glob_base_char_per;
$wisdomAtrb = $glob_base_char_wsd;
$luckAtrb = $glob_base_char_lck;


if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['key']) && !empty($_GET['key'])) {

    $verify_user_email = $_GET['email'];
    $verify_user_hash = $_GET['key'];

    $verify_query = $spdo->prepare('SELECT * FROM users WHERE email = ? AND verify_hash = ? AND verify_user = ?');
    $verify_query->execute([$verify_user_email, $verify_user_hash, 'no']);
    $row = $verify_query->fetch();
    $user_id = $row['id'];
    $username = $row['username'];

    if($verify_user_hash == $row['verify_hash'])
        echo '';
    else
        header("Location: index.php");

    // Warrior Submit

    if(isset($_POST['submit_warrior_class'])){

      $newCharName = strip_tags($_POST['character_name_select']);
      $newCharClass = $_POST['warrior_class_select'];
      $newCharRace = $_POST['character_race_select'];

      $healthStat = $healthStat + ($healthStat * 0.1);
      $manaStat = $manaStat - ($manaStat * 0.1);
      $fatigueStat = $fatigueStat + ($fatigueStat * 0.1);

      switch($newCharRace) {
          case "Human":
              $personalityAtrb = $personalityAtrb + 1;
              $agilityAtrb = $agilityAtrb + 1;
              break;
          case "Elf":
              $intelligenceAtrb = $intelligenceAtrb + 2;
              $agilityAtrb = $agilityAtrb + 2;
              $enduranceAtrb = $enduranceAtrb - 2;
              break;
          case "Lizard":
              $willpowerAtrb = $willpowerAtrb + 2;
              $agilityAtrb = $agilityAtrb + 2;
              $intelligenceAtrb = $intelligenceAtrb - 2;
              break;
          case "Dwarf":
              $strengthAtrb = $strengthAtrb + 2;
              $speedAtrb = $speedAtrb + 2;
              $personalityAtrb = $personalityAtrb - 2;
              break;
          case "Giant":
              $strengthAtrb = $strengthAtrb + 3;
              $enduranceAtrb = $enduranceAtrb + 1;
              $agilityAtrb = $agilityAtrb - 2;
              break;
      }

      switch($newCharClass) {
          case "Archer":
              $agilityAtrb = $agilityAtrb + 2;
              $strengthAtrb = $strengthAtrb + 2;
              break;
          case "Barbarian":
              $strengthAtrb = $strengthAtrb + 2;
              $speedAtrb = $speedAtrb + 2;
              break;
          case "Knight":
              $strengthAtrb = $strengthAtrb + 2;
              $personalityAtrb = $personalityAtrb + 2;
              break;
          case "Crusader":
              $agilityAtrb = $agilityAtrb + 2;
              $wisdomAtrb = $wisdomAtrb + 2;
              break;
          case "Witchhunter": // done
              $intelligenceAtrb = $intelligenceAtrb + 2;
              $agilityAtrb = $agilityAtrb + 2;
              break;
      }

      $newCharQuery = $rpdo->prepare('INSERT INTO rpg_character VALUES (0, 1, ?, ?, ?, 1, ?, ?, ?, 0, 100, 1)');
      $newCharQuery->execute([$newCharName, $newCharClass, $newCharRace, $healthStat, $manaStat, $fatigueStat]);
      $characterId = $rpdo->lastInsertId();

      $newCharLinkQuery = $rpdo->prepare('INSERT INTO user_character VALUES (0, ?, ?)');
      $newCharLinkQuery->execute([$user_id, $characterId]);

      $newCharAtrbQuery = $rpdo->prepare('INSERT INTO character_attribute (character_id, attribute_id, value)
                                                      VALUES (?, 1, ?), (?, 2, ?), (?, 3, ?), (?, 4, ?),
                                                      (?, 5, ?), (?, 6, ?), (?, 7, ?), (?, 8, ?), (?, 9, ?)');
      $newCharAtrbQuery->execute([$characterId, $strengthAtrb, $characterId, $intelligenceAtrb, $characterId, $willpowerAtrb, $characterId, $agilityAtrb, $characterId, $speedAtrb, $characterId, $enduranceAtrb, $characterId, $personalityAtrb, $characterId, $wisdomAtrb, $characterId, $luckAtrb]);

      $new_verify_hash = md5(rand(0,1000));

      $update_user_query = mysqli_query($connect_social, "UPDATE users SET verify_hash='$new_verify_hash', verify_user='yes', user_closed='no' WHERE username='$username'");

      $_SESSION['username'] = $username;
      header("Location: ".$username."");
      exit();


    } // end warrior submit if

    // Mage Submit

    if(isset($_POST['submit_mage_class'])){

      $newCharName = $_POST['character_name_select'];
      $newCharClass = $_POST['mage_class_select'];
      $newCharRace = $_POST['character_race_select'];

      $healthStat = $healthStat + ($healthStat * 0.1);
      $manaStat = $manaStat + ($manaStat * 0.1);
      $fatigueStat = $fatigueStat - ($fatigueStat * 0.1);

      switch($newCharRace) {
          case "Human":
              $personalityAtrb = $personalityAtrb + 1;
              $agilityAtrb = $agilityAtrb + 1;
              break;
          case "Elf":
              $intelligenceAtrb = $intelligenceAtrb + 2;
              $agilityAtrb = $agilityAtrb + 2;
              $enduranceAtrb = $enduranceAtrb - 2;
              break;
          case "Lizard":
              $willpowerAtrb = $willpowerAtrb + 2;
              $agilityAtrb = $agilityAtrb + 2;
              $intelligenceAtrb = $intelligenceAtrb - 2;
              break;
          case "Dwarf":
              $strengthAtrb = $strengthAtrb + 2;
              $speedAtrb = $speedAtrb + 2;
              $personalityAtrb = $personalityAtrb - 2;
              break;
          case "Giant":
              $strengthAtrb = $strengthAtrb + 3;
              $enduranceAtrb = $enduranceAtrb + 1;
              $agilityAtrb = $agilityAtrb - 2;
              break;
      }

      switch($newCharClass) {
          case "Battlemage":
              $intelligenceAtrb = $intelligenceAtrb + 2;
              $strengthAtrb = $strengthAtrb + 2;
              break;
          case "Healer":
              $willpowerAtrb = $willpowerAtrb + 2;
              $personalityAtrb = $personalityAtrb + 2;
              break;
          case "Monk":
              $agilityAtrb = $agilityAtrb + 2;
              $willpowerAtrb = $willpowerAtrb + 2;
              break;
          case "Nightblade":
              $willpowerAtrb = $willpowerAtrb + 2;
              $speedAtrb = $speedAtrb + 2;
              break;
          case "Sorcerer":
              $intelligenceAtrb = $intelligenceAtrb + 2;
              $enduranceAtrb = $enduranceAtrb + 2;
              break;
          case "Spellsword":
              $willpowerAtrb = $willpowerAtrb + 2;
              $enduranceAtrb = $enduranceAtrb + 2;
              break;
      }

      $newCharQuery = $rpdo->prepare('INSERT INTO rpg_character VALUES (0, 1, ?, ?, ?, 1, ?, ?, ?, 0, 100, 1)');
      $newCharQuery->execute([$newCharName, $newCharClass, $newCharRace, $healthStat, $manaStat, $fatigueStat]);
      $characterId = $rpdo->lastInsertId();

      $newCharLinkQuery = $rpdo->prepare('INSERT INTO user_character VALUES (0, ?, ?)');
      $newCharLinkQuery->execute([$user_id, $characterId]);

      $newCharAtrbQuery = $rpdo->prepare('INSERT INTO character_attribute (character_id, attribute_id, value)
                                                      VALUES (?, 1, ?), (?, 2, ?), (?, 3, ?), (?, 4, ?),
                                                      (?, 5, ?), (?, 6, ?), (?, 7, ?), (?, 8, ?), (?, 9, ?)');
      $newCharAtrbQuery->execute([$characterId, $strengthAtrb, $characterId, $intelligenceAtrb, $characterId, $willpowerAtrb, $characterId, $agilityAtrb, $characterId, $speedAtrb, $characterId, $enduranceAtrb, $characterId, $personalityAtrb, $characterId, $wisdomAtrb, $characterId, $luckAtrb]);

      $new_verify_hash = md5(rand(0,1000));

      $update_user_query = mysqli_query($connect_social, "UPDATE users SET verify_hash='$new_verify_hash', verify_user='yes', user_closed='no' WHERE username='$username'");

      $_SESSION['username'] = $username;
      header("Location: ".$username."");
      exit();


    } // end mage submit if

    // Rogue Submit

    if(isset($_POST['submit_rogue_class'])){

      $newCharName = $_POST['character_name_select'];
      $newCharClass = $_POST['rogue_class_select'];
      $newCharRace = $_POST['character_race_select'];

      $healthStat = $healthStat - ($healthStat * 0.1);
      $manaStat = $manaStat + ($manaStat * 0.1);
      $fatigueStat = $fatigueStat + ($fatigueStat * 0.1);

      switch($newCharRace) {
          case "Human":
              $personalityAtrb = $personalityAtrb + 1;
              $agilityAtrb = $agilityAtrb + 1;
              break;
          case "Elf":
              $intelligenceAtrb = $intelligenceAtrb + 2;
              $agilityAtrb = $agilityAtrb + 2;
              $enduranceAtrb = $enduranceAtrb - 2;
              break;
          case "Lizard":
              $willpowerAtrb = $willpowerAtrb + 2;
              $agilityAtrb = $agilityAtrb + 2;
              $intelligenceAtrb = $intelligenceAtrb - 2;
              break;
          case "Dwarf":
              $strengthAtrb = $strengthAtrb + 2;
              $speedAtrb = $speedAtrb + 2;
              $personalityAtrb = $personalityAtrb - 2;
              break;
          case "Giant":
              $strengthAtrb = $strengthAtrb + 3;
              $enduranceAtrb = $enduranceAtrb + 1;
              $agilityAtrb = $agilityAtrb - 2;
              break;
      }

      switch($newCharClass) {
          case "Assassin":
              $intelligenceAtrb = $intelligenceAtrb + 2;
              $speedAtrb = $speedAtrb + 2;
              break;
          case "Acrobat":
              $agilityAtrb = $agilityAtrb + 2;
              $enduranceAtrb = $enduranceAtrb + 2;
              break;
          case "Agent":
              $agilityAtrb = $agilityAtrb + 2;
              $personalityAtrb = $personalityAtrb + 2;
              break;
          case "Bard":
              $intelligenceAtrb = $intelligenceAtrb + 2;
              $personalityAtrb = $personalityAtrb + 2;
              break;
          case "Scout":
              $speedAtrb = $speedAtrb + 2;
              $enduranceAtrb = $enduranceAtrb + 2;
              break;
          case "Thief":
              $speedAtrb = $speedAtrb + 2;
              $agilityAtrb = $agilityAtrb + 2;
              break;
      }

      $newCharQuery = $rpdo->prepare('INSERT INTO rpg_character VALUES (0, 1, ?, ?, ?, 1, ?, ?, ?, 0, 100, 1)');
      $newCharQuery->execute([$newCharName, $newCharClass, $newCharRace, $healthStat, $manaStat, $fatigueStat]);
      $characterId = $rpdo->lastInsertId();

      $newCharLinkQuery = $rpdo->prepare('INSERT INTO user_character VALUES (0, ?, ?)');
      $newCharLinkQuery->execute([$user_id, $characterId]);

      $newCharAtrbQuery = $rpdo->prepare('INSERT INTO character_attribute (character_id, attribute_id, value)
                                                      VALUES (?, 1, ?), (?, 2, ?), (?, 3, ?), (?, 4, ?),
                                                      (?, 5, ?), (?, 6, ?), (?, 7, ?), (?, 8, ?), (?, 9, ?)');
      $newCharAtrbQuery->execute([$characterId, $strengthAtrb, $characterId, $intelligenceAtrb, $characterId, $willpowerAtrb, $characterId, $agilityAtrb, $characterId, $speedAtrb, $characterId, $enduranceAtrb, $characterId, $personalityAtrb, $characterId, $wisdomAtrb, $characterId, $luckAtrb]);

      $new_verify_hash = md5(rand(0,1000));

      $update_user_query = mysqli_query($connect_social, "UPDATE users SET verify_hash='$new_verify_hash', verify_user='yes', user_closed='no' WHERE username='$username'");

      session_start();
      $_SESSION['username'] = $username;
      header("Location: ".$username."");
      exit();


    } // end rogue submit if


} // end verify if
else {
  header("Location: index.php");
}

?>
