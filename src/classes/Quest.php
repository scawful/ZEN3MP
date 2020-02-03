<?php
namespace zen3mp;

class Quest {

	private $user_obj;
	private $rpdo;
	private $spdo;

	public function __construct($user_obj, $rpdo, $spdo) {
		$this->rpdo = $rpdo;
		$this->spdo = $spdo;
		$this->user = new User($user_obj, $spdo);
	}

////////////////////////////////////////////////////////////////////////////////////////////

    public function getNumQuestCategories()
    {
        $stmt = $this->rpdo->prepare('SELECT COUNT(*) FROM quests WHERE active = ?');
        $stmt->execute(["yes"]);
        $num = $stmt->fetchColumn();
        return $num;
    }

    public function addQuestCategory($title, $description, $lvreq, $category) 
    {	  
		$stmt = $this->rpdo->prepare('INSERT INTO quests VALUES(0, ?, ?, ?, ?, ?)');
		$stmt->execute([$title, $description, $lvreq, 'yes', $category]);
    }
    
    public function addQuestPage($title, $body, $media, $authors_note, $quest_id)
    {
        $prpr = $this->rpdo->prepare('SELECT MAX(p_id) FROM quest_pages WHERE q_id = ?');
        $prpr->execute([$quest_id]);
        $page_id = $prpr->fetchColumn();
        if($page_id > 0)
            $page_id++;

        //Current date and time
        $date_added = date("Y-m-d H:i:s");
        $added_by = $this->user->getUsername();

        // temporary variables 
        $location = 1;
        $deleted = 0;

        $stmt = $this->rpdo->prepare('INSERT INTO quest_pages VALUES(0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$quest_id, $page_id, $location, $title, $body, $media, $added_by, $authors_note, $date_added, $deleted]);
    }

    public function deleteQuestPage($q_id, $p_id) 
    {
        $set = 0;
        $prpr = $this->rpdo->prepare('SELECT MAX(p_id) FROM quest_pages WHERE q_id = ?');
        $prpr->execute([$q_id]);
        $page_id = $prpr->fetchColumn();

        $stmt = $this->rpdo->prepare('DELETE FROM quest_pages WHERE q_id = ? AND p_id = ?');
        $stmt->execute([$q_id, $p_id]);

        if($p_id < $page_id)
        {
            $num = $this->getNumQuestPagesByCategory($q_id, $p_id);
            for ($x = 0; $x < $num; $x++) 
            {
                $qry = $this->rpdo->prepare('UPDATE quest_pages SET p_id = ? WHERE q_id = ? AND p_id = ?');
                $qry->execute([$set, $q_id, $x]);
                if ($qry->rowCount() > 0)
                    $set++;
            }
        }

        header("Location: https://zeniea.com/above/?viewpages&q=$q_id");
    }

    public function battleMenu() 
    {
		echo "<div class='btn-group' role='group' aria-label='Commands'>
		        <button type='button' class='btn btn-warning'>Fight</button>
		        <button type='button' class='btn btn-info'>Mana</button>
		        <button type='button' class='btn btn-dark'>Talk</button>
		        <button type='button' class='btn btn-success'>Bag</button>
		        <button type='button' class='btn btn-danger'>Run</button>
		     </div>";
	}

    public function getNumQuestpages()
    {
        $stmt = $this->rpdo->prepare('SELECT COUNT(*) FROM quest_pages');
        $stmt->execute();
        $pages = $stmt->fetchColumn();
        return $pages;
    }

    public function getNumQuestPagesByCategory($id)
    {
        $stmt = $this->rpdo->prepare('SELECT COUNT(*) from quest_pages WHERE q_id = ?');
        $stmt->execute([$id]);
        $num = $stmt->fetchColumn();
        $num--;
        return $num;
    }

    public function getNumQuests()
    {
        $stmt = $this->rpdo->prepare('SELECT COUNT(*) FROM quests WHERE active = ?');
        $stmt->execute(["yes"]);
        $quests = $stmt->fetchColumn();
        return $quests;
    }

    public function getQuestCategoryTitle($id) 
    {
        $stmt = $this->rpdo->prepare('SELECT title FROM quests WHERE id = ?');
        $stmt->execute([$id]);
        $title = $stmt->fetchColumn();
        return $title;
    }

    public function getQuestCategoryDesc($id) 
    {
        $stmt = $this->rpdo->prepare('SELECT tagline FROM quests WHERE id = ?');
        $stmt->execute([$id]);
        $desc = $stmt->fetchColumn();
        return $desc;
    }

    public function getQuestCategoryLevel($id) 
    {
        $stmt = $this->rpdo->prepare('SELECT lvreq FROM quests WHERE id = ?');
        $stmt->execute([$id]);
        $lvl = $stmt->fetchColumn();
        return $lvl;
    }

    public function getQuestPageTitleByCategory($q_id, $p_id)
    {
        $stmt = $this->rpdo->prepare('SELECT title FROM quest_pages WHERE q_id = ? AND p_id = ?');
        $stmt->execute([$q_id, $p_id]);
        $title = $stmt->fetchColumn();
        return $title;
    }

    public function getQuestPageBodyByCategory($q_id, $p_id)
    {
        $stmt = $this->rpdo->prepare('SELECT body FROM quest_pages WHERE q_id = ? AND p_id = ?');
        $stmt->execute([$q_id, $p_id]);
        $body = $stmt->fetchColumn();
        return $body;
    }

    public function getQuestPageBodyPreviewByCategory($q_id, $p_id)
    {
        $stmt = $this->rpdo->prepare('SELECT body FROM quest_pages WHERE q_id = ? AND p_id = ?');
        $stmt->execute([$q_id, $p_id]);
        $body = $stmt->fetchColumn();
        $max = 40;
        $string = $body;
        if (strlen($body) > $max) 
        {
            $shorter = substr($body, 0, $max+1);
            $string = substr($body, 0, strrpos($shorter, ' ')).'...';
        }
        return $string;
    }

    public function getQuestPageMediaByCategory($q_id, $p_id)
    {
        $stmt = $this->rpdo->prepare('SELECT media FROM quest_pages WHERE q_id = ? AND p_id = ?');
        $stmt->execute([$q_id, $p_id]);
        $media = $stmt->fetchColumn();
        return $media;
    }

    public function getQuestPageNoteByCategory($q_id, $p_id)
    {
        $stmt = $this->rpdo->prepare('SELECT author_note FROM quest_pages WHERE q_id = ? AND p_id = ?');
        $stmt->execute([$q_id, $p_id]);
        $note = $stmt->fetchColumn();
        return $note;
    }

    public function getQuestPageAuthorByCategory($q_id, $p_id)
    {
        $stmt = $this->rpdo->prepare('SELECT added_by FROM quest_pages WHERE q_id = ? AND p_id = ?');
        $stmt->execute([$q_id, $p_id]);
        $author = $stmt->fetchColumn();
        return $author;
    }

    public function getQuestPageDateByCategory($q_id, $p_id)
    {
        $stmt = $this->rpdo->prepare('SELECT date FROM quest_pages WHERE q_id = ? AND p_id = ?');
        $stmt->execute([$q_id, $p_id]);
        $date = $stmt->fetchColumn();
        return $date;
    }

    public function getQuestPageCategoryTitle($q_id)
    {
        $stmt2 = $this->rpdo->prepare('SELECT title FROM quests WHERE id = ?');
        $stmt2->execute([$q_id]);
        $quest = $stmt2->fetchColumn();
        return $quest;
    }

}
?>
