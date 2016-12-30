<?php

App::uses('AppModel', 'Model');

class Fighter extends AppModel {

    public $displayField = 'name';

    public $belongsTo = array(
        'Player' => array(
            'className' => 'Player',
            'foreignKey' => 'player_id',
        ),
   );
   
   
  /* SPAWN THE FUCKING FIGHTER */
  	function doSpawn($fid, $arena_num) {
  		$fighter = $this->read(null, $fid);
  		$health = 3+$fighter['Fighter']['skill_health'];
  		$ballSpeed = 22+$fighter['Fighter']['skill_sight'];
  		$playerSpeed = 2+0.08*$fighter['Fighter']['skill_speed'];
  		$level = $fighter['Fighter']['level'];
  		$xp = $fighter['Fighter']['xp'];
  		$this->set('coordinate_x', 0);
  		$this->set('coordinate_y', -10000);
  		$this->set('coordinate_z', 0);
  		$this->set('current_health', $health);
  		$this->set('killed_by', '');
  		$this->set('arena_num', $arena_num);
  		$this->save();
  		
  		return array('ballSpeed' => $ballSpeed, 'health' => $health, 'xp' => $xp, 'playerSpeed' => $playerSpeed, 'level' => $level);
	}
  
   
 /**
 * @todo empecher de sortir des limites de l’arène
 * @todo empecher d'entrer sur une case occupée
 */
	function doMove($fighterId, $posX, $posY, $posZ, $rotY, $animation) {
		
		if ($fighterId == '') return  false;
		$db = $this->getDataSource();
		
		//récupérer la position et fixer l'id de l'attaquant
		$datas = $this->read(null, $fighterId);
		//Avancer selon la direction d'une case
		$this->set('coordinate_x', $posX);
		$this->set('coordinate_y', $posY);
		$this->set('coordinate_z', $posZ);
		$this->set('rotation_y', $rotY);
		$this->set('last_action_time', $db->expression('NOW()') );
		$this->set('animation', $animation );
		
		//sauver la modif
		$this->save();
		return true;
	}
	
	function getArenaNum($fighterId) {
		$datas = $this->read(null, $fighterId);
		return $datas['Fighter']['arena_num'];
	}
	
	function getNumberOfPlayersInArena($numArene) {
		return $this->find('count',  array('conditions' => array('Fighter.arena_num '=> $numArene,
																'Fighter.last_action_time > NOW() - INTERVAL 5 SECOND')
		));
	}
	
	function getArenaNamOf($id) {
		$data = $this->read(null, $id);
		$numArena = $data['Fighter']['arena_num'];
		if ($numArena == 0) {
			return "neige";
		} else if ($numArena == 1) {
			return "herbe";
		} else if ($numArena == 2) {
			return "sable";
		} else if ($numArena == 3) {
			return "bois";
		} else if ($numArena == 4) {
			return "diamant";
		} else {
			return "lave";
		}
		
	}
	
 /**
 * @todo empecher de sortir des limites de l’arène
 * @todo empecher d'entrer sur une case occupée
 */
	function doMoveBall($fighterId, $posX, $posY, $posZ) {
		
		if ($fighterId == '') return  false;
		
		//récupérer la position et fixer l'id de l'attaquant
		$datas = $this->read(null, $fighterId);
		//Avancer selon la direction d'une case
		$this->set('ballpos_x', $posX);
		$this->set('ballpos_y', $posY);
		$this->set('ballpos_z', $posZ);
		
		//sauver la modif
		$this->save();
		return true;
	}


	/* Toucher un joueur */
	function updateLifeAndXP($fid, $tid) {
		//récupérer la position et fixer l'id de l'attaquant
		$fighterMe = $this->find('first', array('conditions' => array('Fighter.id ='=> $fid)));
		$strength = $fighterMe['Fighter']['skill_strength'];
		
		$fighterAutre = $this->read(null, $tid); 
		$oldHealth = $fighterAutre['Fighter']['current_health'];
		$health = $oldHealth - 1 - 0.4*$strength;
		$this->set('current_health', $health);
		if ($health <= 0)
			$this->set('killed_by',$fighterMe['Fighter']['name'] );
		$this->save();
		
		$jeSuisUnBG = $this->read(null, $fid);
		if ($health <= 0 && $oldHealth > 0) {
			if ($jeSuisUnBG['Fighter']['xp'] + 1 >= $jeSuisUnBG['Fighter']['level'] ) {
				$xp = 0;
				$level = $jeSuisUnBG['Fighter']['level'] +1;
			} else {
				$xp = $jeSuisUnBG['Fighter']['xp'] +1;
				$level = $jeSuisUnBG['Fighter']['level'];
			}
			$this->set('xp', $xp);			
			$this->set('level', $level);
			$this->save();
			return array($level,$xp,1);
		} else {
			return array($jeSuisUnBG['Fighter']['level'],$jeSuisUnBG['Fighter']['xp'],0);
		}
	}
	
	function getVitalInfo($fid) {
		$f = $this->read(null, $fid);
		return array($f['Fighter']['current_health'],$f['Fighter']['killed_by']);
	}


 /**
 * @param fighter id: id du fighter a supprimer
 * @param playerId: id du player authentifié
 * @todo mettre une valeur de retour
 */
	function deleteFighter($fighterId, $playerId) {
		$fighter = $this->find('all', array('conditions' => array('Fighter.id ='=> $fighterId)));
		if($fighter && $fighter[0]['Fighter']['player_id'] == $playerId)
			$this->delete($fighterId);
	}
	
 /**
 * @return false si pas d'adversaire sur la case visé
 */
	function doAttack($fighterId, $direction) {
		//récupérer la position et fixer l'id de l'attaquant
		$attaquant = $this->find('first', array('id' => $fighterId));
		// en fonction de la direction d'attaque, 
		// attaquer la persone ce trouvant sur cette case
		$adversaireId = self::findAdversaireId($direction, $attaquant);
		if ($adversaireId != false ) {
			$adversaire = $this->read(null, $adversaireId);
			//retirer point de vie à l'adeversaire attaqué
			$this->set('current_health', $adversaire['Fighter']['current_health'] - 1);
			//sauver la modif
			$this->save();
			return true;
		} 
		return false; 
	}

 /**
 * @param $direction string north / south / east / west => direction de l'attaque
 * @param $attaquant Fighter attaquant
 * @return false si pas d'adversaire sur la case visé
 * @return l'id de l'adversaire
 */
	private function findAdversaireId($direction, $attaquant) {
		switch ($direction) {
			case 'north':
			// case visée
			$coordY = $attaquant['Fighter']['coordinate_y'] + 1;
			$coordX = $attaquant['Fighter']['coordinate_x'];
			
			// verifier la presence d'un adversaire
			$conditions = array("Fighter.coordinate_x" => $coordX, "Fighter.coordinate_y" => $coordY);
			$adversaire = $this->find('first', array('conditions' => $conditions));
			if (empty($adversaire)) return false;
			return $adversaire['Fighter']['id'];
			break;
			
			case 'south':
			// case visée
			$coordY = $attaquant['Fighter']['coordinate_y'] - 1;
			$coordX = $attaquant['Fighter']['coordinate_x'];
			
			// verifier la presence d'un adversaire
			$conditions = array("Fighter.coordinate_x" => $coordX, "Fighter.coordinate_y" => $coordY);
			$adversaire = $this->find('first', array('conditions' => $conditions));
			if (empty($adversaire)) return false;
			return $adversaire['Fighter']['id'];
			break;
			
			case 'east':
			// case visée
			$coordY = $attaquant['Fighter']['coordinate_y'];
			$coordX = $attaquant['Fighter']['coordinate_x'] + 1;
			
			// verifier la presence d'un adversaire
			$conditions = array("Fighter.coordinate_x" => $coordX, "Fighter.coordinate_y" => $coordY);
			$adversaire = $this->find('first', array('conditions' => $conditions));
			if (empty($adversaire)) return false;
			return $adversaire['Fighter']['id'];
			break;
			
			case 'west':
			// case visée
			$coordY = $attaquant['Fighter']['coordinate_y'];
			$coordX = $attaquant['Fighter']['coordinate_x'] - 1;
			
			// verifier la presence d'un adversaire
			$conditions = array("Fighter.coordinate_x" => $coordX, "Fighter.coordinate_y" => $coordY);
			$adversaire = $this->find('first', array('conditions' => $conditions));
			if (empty($adversaire)) return false;
			return $adversaire['Fighter']['id'];
			break;
	
			default: 
			return false;
		}

	}
	
/**
 * @param $playerId player a qui appartien ce nouveau Fighter
 * @param $name nom entré par l'utilisateur
 */
 	function doCreate($name, $playerId) {
	 	
	 	if ($name == '') return false;
	 	if ($this->find('count', array('conditions' => array('name'=>$name)))==0) {
	 		$i=$this->find('count');
 			$a=0;
			$this->create();
			$this->set('name', $name);
			$this->set('player_id', $playerId);
			$this->set('coordinate_x', 1);
			$this->set('coordinate_y', 1);
			$this->set('level', 1);
			$this->set('xp', 0);
			$this->set('avatar_url', 'noPhoto.png');
			$this->set('skill_sight', 0);
			$this->set('skill_strength', 0);
			$this->set('skill_health', 0);
			$this->set('skill_speed', 0);
			$this->set('current_health', 0);
			$this->save();
			return $this->getInsertID();
			//return true;
		} else return false;
 	}

 /**
 * @param $fighterId fighter qui passe de niveau
 */	
 	function doLvlUp($fighterId) {
	 	$datas = $this->read(null, $fighterId);
	 	// up lvl
	 	$this->set('level', $datas['Fighter']['level'] + 1);
	 	// reset xp
	 	$this->set('xp', 0);
	 	//sauver la modif
		$this->save();
 	}
 	
 /**
 * @param $fighterId fighter a qui on ajoute un avatar
 * @param $avatarFile img 
 * @return 'extension' mauvaise extention
 * @return false pas de fichier entré
 */
 	function doUploadAvatar($fighterId, $avatarFile) {
	 	$extension = strtolower(pathinfo($avatarFile['name'], PATHINFO_EXTENSION));
	 	$datas = $this->read(null, $fighterId);
	 	
	 	if ( !empty($avatarFile['tmp_name']) && in_array($extension, array('jpg', 'jpeg', 'png')) ) {
		 	// verifier qu'il n'y a pas déjà un avatar si oui le suprimer
		 	$oldFile = IMAGES . 'avatars' . DS . $datas['Fighter']['avatar_url'];
		 	if (file_exists($oldFile)){
		 		//unlink($oldFile);
		 	}
		 	//upload file et link bdd
		 	move_uploaded_file($avatarFile['tmp_name'], IMAGES . 'avatars' . DS . $fighterId . '.' . $extension);
		 	$this->set('avatar_url', $fighterId . '.' . $extension);
		 	$this->save();
		 	return true;
	 	} else if (!empty($avatarFile['tmp_name'])) {
		 	// bad extension
		 	return 'extension';
	 	} else {
		 	return false;
	 	}
 	}
 	
 	
 	function getName($fighterId) {
		$fighter = $this->read(null, $fighterId);
		return $fighter['Fighter']['name'];
 	}
 	
 	function getLevel($fighterId) {
	 	$fighter = $this->read(null, $fighterId);
		return $fighter['Fighter']['level'];
 	}
 	
 	function getUrlPhoto($fighterId) {
	 	$fighter = $this->read(null, $fighterId);
		return $fighter['Fighter']['avatar_url'];
 	}
 	
 	// return false si aucune
 	function getGuild($fighterId) {
	 	$fighter = $this->read(null, $fighterId);
	 	if ($fighter['Fighter']['guild_id'] == '') {
		 	return false;
	 	}
		return $fighter['Fighter']['guild_id'];
 	}
 
/**
 * @param $fighterId fighter a qui on affiche ses matches
 * @return les id de Matches a afficher
 * @return false si error ou 0
 */
 	function getFighterOnLine($fighterId) {
 		$db = ConnectionManager::getDataSource('default');	
 	 	//récupérer tout les id
		$fighter = $this->find('all', array('conditions' => array(
														  'AND' => array(
														  		  array( 'Fighter.last_action_time > NOW() - INTERVAL 3 SECOND'),
														  		  array( 'Fighter.id !=' => $fighterId)
														  		  ),
														  
											  )
								 ));
								 
								 
		if (!empty($fighter)) {
			return $fighter;
		}
		return array();
 	}
 	
 	function getPositionRotationBallAndAnimOf($fighterId) {
 		$fighter = $this->read(null, $fighterId);
		
		$posRot = array();
		$posRot[] = $fighter['Fighter']['coordinate_x'];
		$posRot[] = $fighter['Fighter']['coordinate_y'];
		$posRot[] = $fighter['Fighter']['coordinate_z'];
		$posRot[] = $fighter['Fighter']['rotation_y'];
		$posRot[] = $fighter['Fighter']['animation'];
		$posRot[] = $fighter['Fighter']['ballpos_x'];
		$posRot[] = $fighter['Fighter']['ballpos_y'];
		$posRot[] = $fighter['Fighter']['ballpos_z'];
		
		return $posRot;
 	}
 	
 	
 	

 /**
 * @param $fighterid id du fighter
 * @return Stats du fighters
 */
 	function getFighterStats($fighterId) {
 		$myData = $this->find('all', array(
 			'conditions' => array(
 				'Fighter.id =' => $fighterId),
 			'fields' =>  array('Fighter.name','Fighter.level', 'Fighter.xp', 
 				'Fighter.avatar_url', 'Fighter.skill_sight', 'Fighter.skill_strength', 
 				'Fighter.skill_health','Fighter.current_health','Fighter.skill_speed')
 			));

 		$stuffStats = $this->getFighterStatsOnlyStuff($fighterId);
 		$numberPointPerLevel = 1;
 		$strength = intval($myData['0']['Fighter']['skill_strength']) - intval($stuffStats['strength']);
 		$sight = intval($myData['0']['Fighter']['skill_sight']) - intval($stuffStats['sight']);
 		$health = intval($myData['0']['Fighter']['skill_health']) - intval($stuffStats['health']);
 		$speed = intval($myData['0']['Fighter']['skill_speed']) - intval($stuffStats['speed']);


 		$skillPoint = intval($myData['0']['Fighter']['level']) * $numberPointPerLevel -
 				($speed + $strength + $health +	$sight);
 		$myData['0']['Fighter']['skill_point'] = $skillPoint;
 		return $myData;
 	}

 /**
 * @param $fighterid id du fighter
 * @return l'ensemble des objets équipé par le personnage
 */
 	function getStuffEquipped($fighterId) {
 		$joins = array(
			  array(
			    'table' => 'inventories',
			    'alias' => 'inventories',
			    'conditions' => array('Fighter.id = inventories.fighter_id')
			  ),
			  array(
			  	'table' => 'tools',
			   	'alias' => 'tools',
			   	'conditions' =>  array('tools.id = inventories.tool_id')
			   )
			);
 		$myData = $this->find('all', array(
 			'conditions' => array(
 				'Fighter.id =' => $fighterId,
 				'inventories.equipped >' => '0'),
 			'joins' => $joins,
 			'order' => array('tools.placement ASC'),
 			'fields' => array('tools.id','tools.health', 'tools.strength','tools.speed', 'tools.sight',
 				'tools.level', 'tools.icon_url', 'tools.description', 'tools.placement', 'tools.description_v')));
 		return $myData;
 	}

/**
 * @param $fighterid id du fighter
 * @return Ensemble des items dans l'inventaire du fighter
 */
 	function getStuffInventory($fighterId) {
 		$joins = array(
			  array(
			    'table' => 'inventories',
			    'alias' => 'inventories',
			    'conditions' => array('Fighter.id = inventories.fighter_id')
			  ),
			  array(
			  	'table' => 'tools',
			   	'alias' => 'tools',
			   	'conditions' =>  array('tools.id = inventories.tool_id')
			   )
			);

 		$myData = $this->find('all', array(
 			'conditions' => array(
 				'Fighter.id =' => $fighterId,
 				'inventories.equipped =' => 'false'),
 			'joins' => $joins,
 			'fields' => array('tools.id','tools.speed', 'tools.strength', 'tools.sight', 'tools.health', 
 				'tools.level', 'tools.icon_url', 'tools.description', 'tools.placement', 'tools.description_v')));
 		
 		return $myData;
 	}

 /**
 * @param $fighterid id du fighter
 * @return ensemble des stats d'un personnage en prenant compte de l'equipement
 */
 	function getFighterStatsWithStuff($fighterId) {
 		$myFighter = $this->getFighterStats($fighterId);
 		$stuffStats = $this->getFighterStatsOnlyStuff($fighterId);
 		$fighterStats = array('strength' => intval($myFighter['0']['Fighter']['skill_strength']), 
 					'speed' => intval($myFighter['0']['Fighter']['skill_speed']), 
 					'sight' => intval($myFighter['0']['Fighter']['skill_sight']), 
 					'health' => intval($myFighter['0']['Fighter']['skill_health']),
 					'skill_point' => intval($myFighter['0']['Fighter']['skill_point']),
 					'strength_item'=> 0, 'speed_item'=> 0,
 					'health_item'=> 0, 'sight_item'=> 0);
 		$fighterStats['strength_item'] += intval($stuffStats['strength']);
 		$fighterStats['speed_item'] += intval($stuffStats['speed']);
 		$fighterStats['sight_item'] += intval($stuffStats['sight']);
 		$fighterStats['health_item'] += intval($stuffStats['health']);
 		return $fighterStats;
 	}

 	function getFighterStatsOnlyStuff($fighterId) {
 		$stuffStat = array('strength_item'=> 0, 'speed_item'=> 0,
 					'health_item'=> 0, 'sight_item'=> 0,
 					'strength'=> 0, 'speed'=> 0,
 					'health'=> 0, 'sight'=> 0);
 		$myStuff = $this->getStuffEquipped($fighterId);
 		foreach ($myStuff as  $items) {

 			foreach ($items['tools'] as  $field => $value) {
 				switch($field){
 					case 'strength':
 						$stuffStat['strength'] += intval($value);
 						break;
 					case 'health':
 						$stuffStat['health'] += intval($value);
 						break;
 					case 'speed':
 						$stuffStat['speed'] += intval($value);
 						break;
 					case 'sight':
 						$stuffStat['sight'] += intval($value);
 						break;
 				}
 			}
 		}
 		return $stuffStat;
 	}
 	function unequipStuff($fighterId) {
 		$stuffStats = $this->getFighterStatsOnlyStuff($fighterId);
 		$myFighterStats = $this->getFighterStats($fighterId);
 		$strength = intval($myFighterStats['0']['Fighter']['skill_strength']) - intval($stuffStats['strength']);
 		$sight = intval($myFighterStats['0']['Fighter']['skill_sight']) - intval($stuffStats['sight']);
 		$health = intval($myFighterStats['0']['Fighter']['skill_health']) - intval($stuffStats['health']);
 		$speed = intval($myFighterStats['0']['Fighter']['skill_speed']) - intval($stuffStats['speed']);
 		$myFighter= array(
 			'Fighter' => array(
 				'skill_strength' => $strength,
 				'skill_sight' => $sight,
 				'skill_health' => $health,
 				'skill_speed' => $speed,
 				'id' => $fighterId
 				)
 			);
 		$this->save($myFighter);
 	}
 	function equipStuff($fighterId) {
 		$stuffStats = $this->getFighterStatsOnlyStuff($fighterId);
 		$myFighterStats = $this->getFighterStats($fighterId);
 		$strength = intval($myFighterStats['0']['Fighter']['skill_strength']) + intval($stuffStats['strength']);
 		$sight = intval($myFighterStats['0']['Fighter']['skill_sight']) + intval($stuffStats['sight']);
 		$health = intval($myFighterStats['0']['Fighter']['skill_health']) + intval($stuffStats['health']);
 		$speed = intval($myFighterStats['0']['Fighter']['skill_speed']) + intval($stuffStats['speed']);
 		$myFighter= array(
 			'Fighter' => array(
 				'skill_strength' => $strength,
 				'skill_sight' => $sight,
 				'skill_health' => $health,
 				'skill_speed' => $speed,
 				'id' => $fighterId
 				)
 			);
 		$this->save($myFighter);
 		return $myFighter;
 	}
 /**
 * @param $playerID joueur actulement en ligne
 * @return les id des fighter appartenant a $playerID
 * @return false si error ou 0 fighter
 */
 	function upSkill($skill, $fighterId) {
 			$checkData = $this->getFighterStats($fighterId);
 			if($checkData[0]['Fighter']['skill_point'] > 0) {
 				switch ($skill) {
 				case 'skill_sight':
 				case 'skill_strength':
 				case 'skill_health':
 				case 'skill_speed':
 					$myData = $this->updateAll(
 						array('Fighter.' . $skill => 'Fighter.' . $skill . " + 1"),
 						array('Fighter.id = ' => $fighterId)
 						);
 					break;
 				default:
 					break;
 				}
 			return $myData;
 			}
 			else {
 				return false;
 			}
	}
 	
 	
/**
 * @param $playerID joueur actulement en ligne
 * @return les id des fighter appartenant a $playerID
 * @return false si error ou 0 fighter
 */
 	function getMyFighter($playerID) {
	 	//récupérer tout les id
		$fighters = $this->find('all', array('conditions' => array('player_id' => $playerID) ));
		if ($fighters != null) {
			return $fighters;
		}
		return false;
 	}
 /**
 * @param $playerID joueur actulement en ligne
 * @return les id des fighter appartenant a $playerID
 * @return false si error ou 0 fighter
 */
 	function removeTool($toolId,$fighterId){}
}
?>