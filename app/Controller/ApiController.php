<?php
App::uses('AppController', 'Controller');
App::uses('Fighter', 'Model');
App::uses('Inventory', 'Model');
APP::uses('Message', 'Model');
APP::uses('Matche', 'Model');

class ApiController extends AppController {
	
 /**
 * @todo Verification de l'id
 * @todo mysql_real_escape_string() pour direction && is_numeric pour id
 */
	function combattantvu($id) {
		if(isset($id)){
			$this->layout = 'ajax';
			$fighter = new Fighter();
			$this->set('datas', $fighter->find('all', array( 'conditions' => array('Fighter.id' => $id))));
		}
	}

 /**
 * @todo verification de l'id et direction
 */
	function combattantdeplacement($id, $direction) {
		if (isset($id) && isset($direction)) {
			$this->layout = 'ajax';
			$fighter = new Fighter();
			switch ($direction) {
				case 'north':	
				case 'south':
				case 'east':				
				case 'weast':
					$this->set('datas', $fighter->doMove($id,$direction));
					break;
				default: 
					$this->set('datas', "Erreur d'utilisation de l'api");
					break;
			}
		}
		
	}

 /**
 * @todo vérification de l'id et direction
 */
	function combattantattaque($id, $direction) {
		if (isset($id) && isset($direction)) {
			$this->layout = 'ajax';
			$fighter = new Fighter();
			switch ($direction) {
				case 'north':	
				case 'south':
				case 'east':				
				case 'weast':
					$this->set('datas', $fighter->doAttack($id,$direction));
					break;
				default: 
					$this->set('datas', "Erreur d'utilisation de l'api");
					break;
			}
		}
	}

/**
 * @todo Enlever le fichier add_message rediriger la vu de cette fonction
 */
	function envoyer_message() {
		//if( $this->request->is('ajax') ) {
			$myMessage = new Message();
			$c = $this->request->data('message');
			if (preg_match("/^http(.*)\.(jpg|png|jpeg|tiff|gif)/", $c))
				$value = '<img style="border-radius:4px" class="img-responsive" src="'.$c.'" alt="" />';
			else if (preg_match("/^(https?):\/\/(.)+\./", $c))
				$value = '<a href="'.$c.'">'.$c.'</a>';
			else
				$value = htmlspecialchars($this->request->data('message'));
			$fighter = new Fighter();
			print_r(intval(htmlspecialchars($this->request->data('fighter_id_to'))));
			if ( $this->request->data('guild') != null )
				$id = $myMessage->addMessage($this->Session->read('current_fighter'), $value,0,$fighter->getGuild($this->Session->read('current_fighter')));
			else if ( $this->request->data('fighter_id_to') != null )
				$id = $myMessage->addMessage($this->Session->read('current_fighter'), $value,intval(htmlspecialchars($this->request->data('fighter_id_to'))));
			else
				$id = $myMessage->addMessage($this->Session->read('current_fighter'), $value, 0);
			die($id);
		//}
	}

/**
 * @todo Enlever le fichier add_message rediriger la vu de cette fonction
 * @todo Verification du message 
 */
	function actualiser_message() {
		//if( $this->request->is('ajax') ) {
			$myMessage = new Message();
			//Verification a rajouter ici pour lastId
			$lastId = htmlspecialchars($this->request->data('dernierMessage'));
			//var_dump($lastId);
			//die();
			//print_r($this->request->data('fighter_id_to'));
			$fighter = new Fighter();
			if ( $this->request->data('guild') != null )
				$myData = $myMessage->getData($lastId,$this->Session->read('current_fighter'),0,$fighter->getGuild($this->Session->read('current_fighter')));
			else if ( $this->request->data('fighter_id_to') != null ){
				//print_r("coucou");
				$myData = $myMessage->getData($lastId, $this->Session->read('current_fighter'), $this->request->data('fighter_id_to'));
			}
			else
            	$myData = $myMessage->getData($lastId, $this->Session->read('current_fighter'), 0);
			$myData = $myMessage->convertData($myData);
			die(json_encode($myData));
		//}
	}
function get_guild_chat() {
	$myFighter = new Fighter();
	print_r($myFighter->getGuild($this->Session->read('current_fighter')));
	die;
}

/**
 * coordX coordY, coordZ et rotation
 */	
	function actualiser_moi () {
		$currentFighter = $this->Session->read('current_fighter');
		if ($this->request->data || 1) {
			$fighter = new Fighter();
			$ret = $fighter->doMove($currentFighter, 
											$this->request->data('px'),
											$this->request->data('py'),
											$this->request->data('pz'),
											$this->request->data('ry'),
											$this->request->data('animation'));
											
			$ball = $fighter->doMoveBall($currentFighter, 
										 $this->request->data('bx'),
										 $this->request->data('by'), 
										 $this->request->data('bz'));
			$infomoi = $fighter->getVitalInfo($currentFighter);	
    		die(json_encode($infomoi));
		}
	}
	
/**
 * Joueur en ligne sur le plateau
 */	
	function recuperer_liste_joueur () {
		$currentFighter = $this->Session->read('current_fighter');
		$fighter = new Fighter();
		$fighters = $fighter->getFighterOnLine($currentFighter);
		$res = array();
		foreach ($fighters as $f) {
			
			if ($f['Fighter']['arena_num'] != $fighter->getArenaNum($currentFighter) ) {
				continue;
			}
			
			$res[] = array($f["Fighter"]["id"], $f["Fighter"]["name"], 22+((int)$f["Fighter"]["skill_sight"]) , 2+0.08*((int)$f["Fighter"]["skill_speed"]) );
			$equip = $fighter->getStuffEquipped($f["Fighter"]["id"]);
			$res[count($res)-1][] = isset($equip[0]) ? $equip[0]['tools']['description'] : "w_machinegun";
			$res[count($res)-1][] = isset($equip[1]) ? $equip[1]['tools']['description'] : "ratamahatta";
		}
		die(json_encode($res));
	}
	
/**
 * coordX coordY, coordZ et rotation
 */	
 	function recuperer_info_joueur() {
	 	if ($this->request->data) {
		 	$fighter = new Fighter();
		 	$posRot = $fighter->getPositionRotationBallAndAnimOf($this->request->data('fighterId'));
		 	die(json_encode($posRot));
	 	}
	 	die("fail");	
 	}
 	
 	function toucher_joueur() {
	 	$currentFighter = $this->Session->read('current_fighter');
	 	$touchedFighter = $this->request->data('tid');
	 	$fighter = new Fighter();
	 	$resTouch = $fighter->updateLifeAndXP($currentFighter, $touchedFighter);
	 	if ($resTouch[2] == 1) {
		 	$inv = new Inventory();
			$resTouch[] = $inv->tryLoot($currentFighter, $resTouch[0]);
			$match = new Matche();
			$match->logMatch($currentFighter,$touchedFighter);
		} else {
			$resTouch[] = "";
		}
	 	die(json_encode($resTouch));
 	}
	
}

?>
