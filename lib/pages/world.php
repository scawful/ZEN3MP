<?php
if(isset($_GET['q']))
    $quest_id = $_GET['q'];
else {
    $quest_id = 0;
}


?>

<div id="mainBox" class="boxes">

    <?php if($quest_id == 0) { ?>

      <div class="card">
        <h5 class="card-header">Main Quests</h5>
          <div class="card-body">
            <?php $quest_obj->listQuests(1); ?>
          </div>

        <h5 class="card-header">Wild Areas</h5>
          <div class="card-body">
            <?php $quest_obj->listQuests(2); ?>
          </div>
      </div>

    <?php
    } else {
      if(isset($_GET['p'])) {
        $page_id = $_GET['p'];
        $quest_obj->loadPage($page_id);
      } else {
        $quest_obj->showQuestHome($quest_id);
      }
    }
    ?>

    <br />

</div>





  <?php include ('lib/pages/profile_box.php'); ?>

  <div id="sideBox" class="boxes">
    <div class="titleBar">Activity</div>
    <br>
    <ul>
      <li>Active Quests: 0</li>
      <li>Players Online: N/A</li>
    </ul>
  </div>
