
    <h4>Create Character Form</h4>
    <h5>Select your race and class type below to begin.</h5>


    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="width: 26%; float: left;">
      <a class="nav-link active" id="v-pills-warrior-tab" data-toggle="pill" href="#v-pills-warrior" role="tab" aria-controls="v-pills-warrior" aria-selected="true">Warrior</a>
      <a class="nav-link" id="v-pills-mage-tab" data-toggle="pill" href="#v-pills-mage" role="tab" aria-controls="v-pills-mage" aria-selected="false">Mage</a>
      <a class="nav-link" id="v-pills-rogue-tab" data-toggle="pill" href="#v-pills-rogue" role="tab" aria-controls="v-pills-rogue" aria-selected="false">Rogue</a>
      <a class="nav-link disabled" id="v-pills-custom-tab" data-toggle="pill" href="#v-pills-custom" role="tab" aria-controls="v-pills-custom" aria-selected="false">Custom (Disabled)</a>
      <a class="nav-link disabled" id="v-pills-god-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-god" aria-selected="false">God (Disabled)</a>
    </div>

<!-- WARRIOR CATEGORY -->

<div class="tab-content" id="v-pills-tabContent" style="float: right; width: 70%; margin: 5px;">
    <div class="tab-pane fade show active" id="v-pills-warrior" role="tabpanel" aria-labelledby="v-pills-warrior-tab">
      <h4>Warrior Classes:</h4>

      <p> +10% Health | -10% Mana | +10% Fatigue
      <br>Select any of the options to view details about them.</p>


      <form class="" action="" method="POST">
      <?php include("race_radio.html"); ?>


        <!-- Warrior Class Select Info -->
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="pills-archer-tab" data-toggle="pill" href="#pills-archer" role="tab" aria-controls="pills-archer" aria-selected="true">Archer</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="pills-barbarian-tab" data-toggle="pill" href="#pills-barbarian" role="tab" aria-controls="pills-barbarian" aria-selected="false">Barbarian</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="pills-knight-tab" data-toggle="pill" href="#pills-knight" role="tab" aria-controls="pills-knight" aria-selected="false">Knight</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="pills-crusader-tab" data-toggle="pill" href="#pills-crusader" role="tab" aria-controls="pills-crusader" aria-selected="false">Crusader</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="pills-witchhunter-tab" data-toggle="pill" href="#pills-witchhunter" role="tab" aria-controls="pills-witchhunter" aria-selected="false">Witchhunter</a>
          </li>

        </ul>
        <div class="tab-content" id="pills-tabContent">
          <div class="tab-pane fade show active" id="pills-archer" role="tabpanel" aria-labelledby="pills-archer-tab">
            +2 Agility, +2 Strength
          </div>
          <div class="tab-pane fade" id="pills-barbarian" role="tabpanel" aria-labelledby="pills-barbarian-tab">
            +2 Speed, +2 Strength
          </div>
          <div class="tab-pane fade" id="pills-knight" role="tabpanel" aria-labelledby="pills-knight-tab">
            +2 Strength, +2 Personality
          </div>
          <div class="tab-pane fade" id="pills-crusader" role="tabpanel" aria-labelledby="pills-crusader-tab">
            +2 Agility, +2 Wisdom
          </div>
          <div class="tab-pane fade" id="pills-witchhunter" role="tabpanel" aria-labelledby="pills-witchhunter-tab">
            +2 Intelligence, +2 Agility
          </div>
        </div>

        <br>
          <select class="custom-select custom-select-lg" id="warrior_class_select" style="margin-bottom: 10px;" name="warrior_class_select" required>
            <option selected disabled>Select your class from this menu.</option>
            <option value="Archer">Archer</option>
            <option value="Barbarian">Barbarian</option>
            <option value="Knight">Knight </option>
            <option value="Crusader">Crusader</option>
            <option value="Witchhunter">Witchhunter</option>
          </select>

        <button class="btn btn-success w-100" name="submit_warrior_class">Submit</button>
        </form>

    </div>

<!-- MAGES CATEGORY -->

  <div class="tab-pane fade" id="v-pills-mage" role="tabpanel" aria-labelledby="v-pills-mage-tab">

    <h4>Mage Classes:</h4>

    <p> +10% Health | +10% Mana | -10% Fatigue
    <br>Select any of the options to view details about them.</p>


    <form class="" action="" method="POST">
    <?php include("race_radio.html"); ?>

      <!-- Warrior Class Select Info -->
      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="pills-battlemage-tab" data-toggle="pill" href="#pills-battlemage" role="tab" aria-controls="pills-battlemage" aria-selected="true">Battlemage</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="pills-healer-tab" data-toggle="pill" href="#pills-healer" role="tab" aria-controls="pills-healer" aria-selected="false">Healer</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="pills-monk-tab" data-toggle="pill" href="#pills-monk" role="tab" aria-controls="pills-monk" aria-selected="false">Monk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="pills-nightblade-tab" data-toggle="pill" href="#pills-nightblade" role="tab" aria-controls="pills-nightblade" aria-selected="false">Nightblade</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="pills-sorcerer-tab" data-toggle="pill" href="#pills-sorcerer" role="tab" aria-controls="pills-sorcerer" aria-selected="false">Sorcerer</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="pills-spellsword-tab" data-toggle="pill" href="#pills-sorcerer" role="tab" aria-controls="pills-spellsword" aria-selected="false">Spellsword</a>
        </li>


      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-battlemage" role="tabpanel" aria-labelledby="pills-battlemage-tab">
          +2 Intelligence, +2 Strength
        </div>
        <div class="tab-pane fade" id="pills-healer" role="tabpanel" aria-labelledby="pills-healer-tab">
          +2 Willpower, +2 Personality
        </div>
        <div class="tab-pane fade" id="pills-monk" role="tabpanel" aria-labelledby="pills-monk-tab">
          +2 Agility, +2 Willpower
        </div>
        <div class="tab-pane fade" id="pills-nightblade" role="tabpanel" aria-labelledby="pills-nightblade-tab">
          +2 Willpower, +2 Speed
        </div>
        <div class="tab-pane fade" id="pills-sorcerer" role="tabpanel" aria-labelledby="pills-sorcerer-tab">
          +2 Intelligence, +2 Endurance
        </div>
        <div class="tab-pane fade" id="pills-spellsword" role="tabpanel" aria-labelledby="pills-spellsword-tab">
          +2 Willpower, +2 Endurance
        </div>
      </div>

      <br>
        <select class="custom-select custom-select-lg" id="mage_class_select" style="margin-bottom: 10px;" name="mage_class_select" required>
          <option selected disabled>Select your class from this menu.</option>
          <option value="Battlemage">Battlemage</option>
          <option value="Healer">Healer</option>
          <option value="Monk">Monk</option>
          <option value="Nightblade">Nightblade</option>
          <option value="Sorcerer">Sorcerer</option>
          <option value="Spellsword">Spellsword</option>
        </select>

      <button class="btn btn-success w-100" name="submit_mage_class">Submit</button>
      </form>


  </div>

  <!-- ROGUE CATEGORY -->

  <div class="tab-pane fade" id="v-pills-rogue" role="tabpanel" aria-labelledby="v-pills-rogue-tab">

    <h4>Rogue Classes:</h4>

    <p> -10% Health | +10% Mana | +10% Fatigue
    <br>Select any of the options to view details about them.</p>


    <form class="" action="" method="POST">
    <?php include("race_radio.html"); ?>

    <!-- Rogue Class Select Info -->
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="pills-assassin-tab" data-toggle="pill" href="#pills-assassin" role="tab" aria-controls="pills-assassin" aria-selected="true">Assassin</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="pills-arcobat-tab" data-toggle="pill" href="#pills-acrobat" role="tab" aria-controls="pills-acrobat" aria-selected="false">Acrobat</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="pills-agent-tab" data-toggle="pill" href="#pills-agent" role="tab" aria-controls="pills-agent" aria-selected="false">Agent</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="pills-bard-tab" data-toggle="pill" href="#pills-bard" role="tab" aria-controls="pills-bard" aria-selected="false">Bard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="pills-scout-tab" data-toggle="pill" href="#pills-sorcerer" role="tab" aria-controls="pills-scout" aria-selected="false">Scout</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="pills-thief-tab" data-toggle="pill" href="#pills-thief" role="tab" aria-controls="pills-thief" aria-selected="false">Thief</a>
      </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
      <div class="tab-pane fade show active" id="pills-assassin" role="tabpanel" aria-labelledby="pills-assassin-tab">
        +2 Speed, +2 Intelligence
      </div>
      <div class="tab-pane fade" id="pills-acrobat" role="tabpanel" aria-labelledby="pills-acrobat-tab">
        +2 Agility, +2 Endurance
      </div>
      <div class="tab-pane fade" id="pills-agent" role="tabpanel" aria-labelledby="pills-agent-tab">
        +2 Personality, +2 Agility
      </div>
      <div class="tab-pane fade" id="pills-bard" role="tabpanel" aria-labelledby="pills-bard-tab">
        +2 Personality, +2 Intelligence
      </div>
      <div class="tab-pane fade" id="pills-scout" role="tabpanel" aria-labelledby="pills-scout-tab">
        +2 Speed, +2 Endurance
      </div>
      <div class="tab-pane fade" id="pills-thief" role="tabpanel" aria-labelledby="pills-thief-tab">
        +2 Speed, +2 Agility
      </div>
    </div>


    <br>
    <select class="custom-select custom-select-lg" id="rogue_class_select" style="margin-bottom: 10px;" name="rogue_class_select">
      <option selected disabled>Select from this menu.</option>
        <option value="Assassin">Assassin</option>
        <option value="Acrobat">Acrobat</option>
        <option value="Agent">Agent</option>
        <option value="Bard">Bard</option>
        <option value="Scout">Scout</option>
        <option value="Thief">Thief</option>
      </select>

      <br />

      <button class="btn btn-success w-100" name="submit_rogue_class">Submit</button>
    </form>

  </div>

  <!-- CUSTOM CATEGORY -->

  <div class="tab-pane fade" id="v-pills-custom" role="tabpanel" aria-labelledby="v-pills-custom-tab">

    <h4>Currently Unavailable</h4>

  </div>

</div>
