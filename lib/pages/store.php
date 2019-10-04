

<div id="mainBox" class="boxes">
  <div class="titleBar">Item Store</div>

  <div class="article">
    <?php
    $inventory = new Inventory($connect_social, $connect_rpg, $userLoggedIn, $spdo);

    if (!empty($_GET['item']) && (intval($_GET['item']) == $_GET['item'])) {
      echo "<a href='?store'><button type='button' class='btn btn-primary'>Return</button></a><br>";
      $solo_item = $inventory->getItemInfo($_GET['item']);
    } else {
      ?>

  </div>

  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link active" href="#general" aria-controls="general" data-toggle="tab" aria-selected="true">General</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#weapons" aria-controls="weapons" data-toggle="tab" aria-selected="true">Weapons</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#armor" aria-controls="armor" data-toggle="tab" aria-selected="true">Armor</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#magic" aria-controls="magic" data-toggle="tab" aria-selected="true">Magic</a>
    </li>
  </ul>

  <div class="tab-content" id="myTabContent">
<!—                                                                                                  —>
      <div role="tabpanel" class="tab-pane fade show active" id="general" aria-labelledby="general">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">Price</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
          <?php $list_items = $inventory->listItems('1'); ?>
          </tbody>
        </table>
      </div>
<!—                                                                                                  —>
<div role="tabpanel" class="tab-pane fade" id="weapons" aria-labelledby="weapons">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Description</th>
          <th scope="col">Price</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $list_items = $inventory->listItems('2');
         ?>
      </tbody>
    </table>

</div>
<!—                                                                                                  —>
<div role="tabpanel" class="tab-pane fade" id="armor" aria-labelledby="armor">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Description</th>
          <th scope="col">Price</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $list_items = $inventory->listItems('3');
         ?>
      </tbody>
    </table>

</div>
<!—                                                                                                  —>
<div role="tabpanel" class="tab-pane fade" id="magic" aria-labelledby="magic">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Description</th>
          <th scope="col">Price</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $list_items = $inventory->listItems('4');
         ?>
      </tbody>
    </table>

</div>
<?php } ?>

  </div>
</div>


<?php include("lib/pages/profile_box.php"); ?>

  <div id="sideBox" class="boxes">
    <div class="titleBar">Economy</div>
      <div class="article">
    <?php
    $economy_sum = $character->getAllCharacterMoney();
    $full_sum = $inventory->getInventoryValue();
    ?>
      </div>
  </div>
