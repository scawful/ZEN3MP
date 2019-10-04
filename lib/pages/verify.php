<?php
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


    $verify_query = mysqli_query($connect_social, "SELECT * FROM users WHERE email='$verify_user_email' AND verify_hash='$verify_user_hash' AND verify_user='no'");
    $row = mysqli_fetch_array($verify_query);
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

      $newCharQuery = mysqli_query($connect_rpg, "INSERT INTO rpg_character VALUES (0, 1, '$newCharName', '$newCharClass', '$newCharRace', 1, $healthStat, $manaStat, $fatigueStat, 0, 100, 1)");

      $characterId = mysqli_insert_id($connect_rpg);

      $newCharLinkQuery = mysqli_query($connect_rpg, "INSERT INTO user_character VALUES (0, $user_id, $characterId)");

      $newCharAtrbQuery = mysqli_query($connect_rpg, "INSERT INTO character_attribute (character_id, attribute_id, value)
                                                      VALUES ($characterId, 1, $strengthAtrb),
                                                      ($characterId, 2, $intelligenceAtrb),
                                                      ($characterId, 3, $willpowerAtrb),
                                                      ($characterId, 4, $agilityAtrb),
                                                      ($characterId, 5, $speedAtrb),
                                                      ($characterId, 6, $enduranceAtrb),
                                                      ($characterId, 7, $personalityAtrb),
                                                      ($characterId, 8, $wisdomAtrb),
                                                      ($characterId, 9, $luckAtrb)");
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

      $newCharQuery = mysqli_query($connect_rpg, "INSERT INTO rpg_character VALUES (0, 1, '$newCharName', '$newCharClass', '$newCharRace', 1, $healthStat, $manaStat, $fatigueStat, 0, 100, 1)");

      $characterId = mysqli_insert_id($connect_rpg);

      $newCharLinkQuery = mysqli_query($connect_rpg, "INSERT INTO user_character VALUES (0, $user_id, $characterId)");

      $newCharAtrbQuery = mysqli_query($connect_rpg, "INSERT INTO character_attribute (character_id, attribute_id, value)
                                                      VALUES ($characterId, 1, $strengthAtrb),
                                                      ($characterId, 2, $intelligenceAtrb),
                                                      ($characterId, 3, $willpowerAtrb),
                                                      ($characterId, 4, $agilityAtrb),
                                                      ($characterId, 5, $speedAtrb),
                                                      ($characterId, 6, $enduranceAtrb),
                                                      ($characterId, 7, $personalityAtrb),
                                                      ($characterId, 8, $wisdomAtrb),
                                                      ($characterId, 9, $luckAtrb)");

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

      $newCharQuery = mysqli_query($connect_rpg, "INSERT INTO rpg_character VALUES (0, 1, '$newCharName', '$newCharClass', '$newCharRace', 1, $healthStat, $manaStat, $fatigueStat, 0, 100, 1)");

      $characterId = mysqli_insert_id($connect_rpg);

      $newCharLinkQuery = mysqli_query($connect_rpg, "INSERT INTO user_character VALUES (0, $user_id, $characterId)");

      $newCharAtrbQuery = mysqli_query($connect_rpg, "INSERT INTO character_attribute (character_id, attribute_id, value)
                                                      VALUES ($characterId, 1, $strengthAtrb),
                                                      ($characterId, 2, $intelligenceAtrb),
                                                      ($characterId, 3, $willpowerAtrb),
                                                      ($characterId, 4, $agilityAtrb),
                                                      ($characterId, 5, $speedAtrb),
                                                      ($characterId, 6, $enduranceAtrb),
                                                      ($characterId, 7, $personalityAtrb),
                                                      ($characterId, 8, $wisdomAtrb),
                                                      ($characterId, 9, $luckAtrb)");

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
<div id="mainBox" class="boxes">
<div class="column">

  <p>Welcome to the character creation screen! In order to complete the process of verifying your account you'll have to create an RPG character to use on the site. As of right now there is no editor for characters after creation and this layout can be a bit confusing so choose carefully. For the character class you have to select from the dropdown menu to properly input it. The main buttons only show the stat differences between each race.</p>

</div>


<div class="article">

    <?php include("lib/pages/char_sheet.php"); ?>

</div>
</div>

<div id="sideBox" class="boxes">
  <div class="titleBar">Ads served by Google</div>
  <br />
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- TallBoi -->
  <ins class="adsbygoogle"
       style="display:block"
       data-ad-client="ca-pub-8036786549441922"
       data-ad-slot="1140152702"
       data-ad-format="auto"
       data-full-width-responsive="true"></ins>
  <script>
       (adsbygoogle = window.adsbygoogle || []).push({});
  </script>
</div>
