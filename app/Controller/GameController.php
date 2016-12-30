<?php 

App::uses('AppController', 'Controller');
define('MAX_PLAYERS_IN_ARENA',5);
/**
 * LE JEU 3D TINITIN L'AMIE
 */
class GameController extends AppController {
	
	public $uses = array('Fighter');
	
	public function beforeRender() {
        $this->layout = AJAX_LAYOUT;
		if ($this->params["action"] == "index")
		    $this->layout = MINIMALIST_LAYOUT;
    }
    
    public function beforeFilter() {
	    if (!$this->Auth->user('id')) {
		    $this->redirect(LOGIN_LINK);
	    }
        if ($this->Auth->User('id') != null && $this->Session->read('current_fighter') == null && $this->here != '/arenas/myfighters') {
        	$this->redirect('/arenas/myfighters');
        }
    
    	$nomsArene = array('neige','herbe','sable','bois', 'diamant', 'lave');
		foreach ($nomsArene as $i => $n) {
			if (strpos($this->params["action"], $n))
				break;
		}
		if ($this->Fighter->getNumberOfPlayersInArena($i) >= MAX_PLAYERS_IN_ARENA && $this->params["action"] != "index") {
			$this->redirect('/game');
			exit();
		}
    }
    
    public function index() {
	    $this->set('numberOfArena', 7);
	    $this->set('areneName', array('neige','herbe','sable','bois', 'diamant', 'lave', 'roche') );
	    $this->set('numDeJoueur', array($this->Fighter->getNumberOfPlayersInArena(0),$this->Fighter->getNumberOfPlayersInArena(1),
	    								$this->Fighter->getNumberOfPlayersInArena(2),$this->Fighter->getNumberOfPlayersInArena(3), 
	    								$this->Fighter->getNumberOfPlayersInArena(4), $this->Fighter->getNumberOfPlayersInArena(5),
	    								$this->Fighter->getNumberOfPlayersInArena(6)) );
    }
    
    public function arene_neige() {
		$currentFighter = $this->Session->read('current_fighter');
		$params = $this->Fighter->doSpawn($currentFighter, 0);
		$this->set('ballSpeed', $params["ballSpeed"]);
		$this->set('health', $params["health"]);
		$this->set('xp', $params["xp"]);
		$this->set('level', $params["level"]);
		$this->set('playerSpeed', $params["playerSpeed"]);
		$equip = $this->Fighter->getStuffEquipped($currentFighter);
		$this->set('weapon', isset($equip[0]) ? $equip[0]['tools']['description'] : "w_machinegun");
		$this->set('skin', isset($equip[1]) ? $equip[1]['tools']['description'] : "ratamahatta");
		//var_dump($params);die();
    }
    
    public function arene_herbe() {
		$currentFighter = $this->Session->read('current_fighter');
		$params = $this->Fighter->doSpawn($currentFighter, 1);
		$this->set('ballSpeed', $params["ballSpeed"]);
		$this->set('health', $params["health"]);
		$this->set('xp', $params["xp"]);
		$this->set('level', $params["level"]);
		$this->set('playerSpeed', $params["playerSpeed"]);
		$equip = $this->Fighter->getStuffEquipped($currentFighter);
		$this->set('weapon', isset($equip[0]) ? $equip[0]['tools']['description'] : "w_machinegun");
		$this->set('skin', isset($equip[1]) ? $equip[1]['tools']['description'] : "ratamahatta");
		//var_dump($params);die();
    }
    
    public function arene_sable() {
		$currentFighter = $this->Session->read('current_fighter');
		$params = $this->Fighter->doSpawn($currentFighter, 2);
		$this->set('ballSpeed', $params["ballSpeed"]);
		$this->set('health', $params["health"]);
		$this->set('xp', $params["xp"]);
		$this->set('level', $params["level"]);
		$this->set('playerSpeed', $params["playerSpeed"]);
		$equip = $this->Fighter->getStuffEquipped($currentFighter);
		$this->set('weapon', isset($equip[0]) ? $equip[0]['tools']['description'] : "w_machinegun");
		$this->set('skin', isset($equip[1]) ? $equip[1]['tools']['description'] : "ratamahatta");
		//var_dump($params);die();
    }
    
    public function arene_bois() {
		$currentFighter = $this->Session->read('current_fighter');
		$params = $this->Fighter->doSpawn($currentFighter, 3);
		$this->set('ballSpeed', $params["ballSpeed"]);
		$this->set('health', $params["health"]);
		$this->set('xp', $params["xp"]);
		$this->set('level', $params["level"]);
		$this->set('playerSpeed', $params["playerSpeed"]);
		$equip = $this->Fighter->getStuffEquipped($currentFighter);
		$this->set('weapon', isset($equip[0]) ? $equip[0]['tools']['description'] : "w_machinegun");
		$this->set('skin', isset($equip[1]) ? $equip[1]['tools']['description'] : "ratamahatta");
		//var_dump($params);die();
    }
    
    public function arene_diamant() {
		$currentFighter = $this->Session->read('current_fighter');
		$params = $this->Fighter->doSpawn($currentFighter, 4);
		$this->set('ballSpeed', $params["ballSpeed"]);
		$this->set('health', $params["health"]);
		$this->set('xp', $params["xp"]);
		$this->set('level', $params["level"]);
		$this->set('playerSpeed', $params["playerSpeed"]);
		$equip = $this->Fighter->getStuffEquipped($currentFighter);
		$this->set('weapon', isset($equip[0]) ? $equip[0]['tools']['description'] : "w_machinegun");
		$this->set('skin', isset($equip[1]) ? $equip[1]['tools']['description'] : "ratamahatta");
		//var_dump($params);die();
    }
    
    public function arene_lave() {
		$currentFighter = $this->Session->read('current_fighter');
		$params = $this->Fighter->doSpawn($currentFighter, 5);
		$this->set('ballSpeed', $params["ballSpeed"]);
		$this->set('health', $params["health"]);
		$this->set('xp', $params["xp"]);
		$this->set('level', $params["level"]);
		$this->set('playerSpeed', $params["playerSpeed"]);
		$equip = $this->Fighter->getStuffEquipped($currentFighter);
		$this->set('weapon', isset($equip[0]) ? $equip[0]['tools']['description'] : "w_machinegun");
		$this->set('skin', isset($equip[1]) ? $equip[1]['tools']['description'] : "ratamahatta");
		//var_dump($params);die();
    }

    public function arene_roche() {
		$currentFighter = $this->Session->read('current_fighter');
		$params = $this->Fighter->doSpawn($currentFighter, 6);
		$this->set('ballSpeed', $params["ballSpeed"]);
		$this->set('health', $params["health"]);
		$this->set('xp', $params["xp"]);
		$this->set('level', $params["level"]);
		$this->set('playerSpeed', $params["playerSpeed"]);
		$equip = $this->Fighter->getStuffEquipped($currentFighter);
		$this->set('weapon', isset($equip[0]) ? $equip[0]['tools']['description'] : "w_machinegun");
		$this->set('skin', isset($equip[1]) ? $equip[1]['tools']['description'] : "ratamahatta");
		//var_dump($params);die();
    }
}

?>