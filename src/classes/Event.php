<?php
namespace zen3mp;

class Event 
{
	public function __construct($user, $character, $spdo, $rpdo) 
	{
        $this->user_obj = new User($user, $spdo);
        $this->character_obj = $character;
		$this->spdo = $spdo;
        $this->rpdo = $rpdo;
    }

}
?>