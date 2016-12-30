<?php 

App::uses('AppController', 'Controller');
APP::uses('Message', 'Model');
/**
 * Main controller of our small application
 *
 */
class ArenasController extends AppController {

	/* Specify uses */
	public $uses = array('Player', 'Fighter', 'Event', 'Matche', 'Guild', 'Inventory', 'Message',);

	public function beforeRender() {
		$this->layout = MINIMALIST_LAYOUT;
	}
	/**
	* index method : first page
	*
	* @return void
	*/
	public function index() {

	}
	
	
	/**
	* login method : 
	*
	* @return void
	*/
	public function login() {

	}
	
	
	/**
	* fighter method : doCreate && doLvlUp && doChooseAvatar
	*
	* @return void
	*/
	public function combattant() {
		if ($this->request->is('post')) {
			if (isset($this->request->data['FighterCreate']['name'])) {
				// Creat fighter
				$return = $this->Fighter->doCreate(1, $this->request->data['FighterCreate']['name']);
			} else if (isset($this->request->data['FighterLvlUp']['fighterId'])) {
				// fighter lvl up
				$this->Fighter->doLvlUp($this->request->data['FighterLvlUp']['fighterId']);
			} else if (isset($this->request->data['FighterChooseAvatar']['avatar_file'])) {
				// upload avatar
				$return = $this->Fighter->doUploadAvatar(1, $this->request->data['FighterChooseAvatar']['avatar_file']);
			}
		}

		$this->set('raw',$this->Fighter->find("all"));
	}
	
	
	/**
	* sight method : doMove && doAttack
	*
	* @return void
	*/
	public function action() {
		$Fid=$this->Session->read('current_fighter');
		$monfighter = $this->Fighter->find('first', array('conditions' => array('Fighter.id' => $Fid)));
		if ($this->request->is('post')) {
			 if (isset($this->request->data['Fightermove']['direction'])) {
			 	// move fighter
			 	switch ($this->request->data['Fightermove']['direction']) {
			 		case 'north':
			 			$x = -1;
			 			$y = 0;
			 			if(intval($monfighter['Fighter']['coordinate_x']) > -15) {
			 				$this->Fighter->doMove($Fid, intval($monfighter['Fighter']['coordinate_x']+$x), intval($monfighter['Fighter']['coordinate_y']+$y), $monfighter['Fighter']['coordinate_z'], $monfighter['Fighter']['rotation_y'], $monfighter['Fighter']['animation']);
			 			}
			 			break;
			 		case 'east':
			 			$x = 0;
			 			$y = 1;
			 			if(intval($monfighter['Fighter']['coordinate_y']) < 15) {
			 				$this->Fighter->doMove($Fid, intval($monfighter['Fighter']['coordinate_x']+$x), intval($monfighter['Fighter']['coordinate_y']+$y), $monfighter['Fighter']['coordinate_z'], $monfighter['Fighter']['rotation_y'], $monfighter['Fighter']['animation']);
			 			}
			 			break;
			 		case 'south':
			 			$x = 1;
			 			$y = 0;
			 			if(intval($monfighter['Fighter']['coordinate_x']) < 15) {
			 				$this->Fighter->doMove($Fid, intval($monfighter['Fighter']['coordinate_x']+$x), intval($monfighter['Fighter']['coordinate_y']+$y), $monfighter['Fighter']['coordinate_z'], $monfighter['Fighter']['rotation_y'], $monfighter['Fighter']['animation']);
			 			}
			 			break;
			 		case 'west':
			 			$x = 0;
			 			$y = -1;
			 			if(intval($monfighter['Fighter']['coordinate_y']) > -15) {
			 				$this->Fighter->doMove($Fid, intval($monfighter['Fighter']['coordinate_x']+$x), intval($monfighter['Fighter']['coordinate_y']+$y), $monfighter['Fighter']['coordinate_z'], $monfighter['Fighter']['rotation_y'], $monfighter['Fighter']['animation']);
			 			}
			 			break;
			 		default :
			 			$x = 0;
			 			$y = 1;
			 			if(intval($monfighter['Fighter']['coordinate_y']) < 15) {
			 				$this->Fighter->doMove($Fid, intval($monfighter['Fighter']['coordinate_x']+$x), intval($monfighter['Fighter']['coordinate_y']+$y), $monfighter['Fighter']['coordinate_z'], $monfighter['Fighter']['rotation_y'], $monfighter['Fighter']['animation']);
			 			}
			 			break;
			 	}
			 } else if (isset($this->request->data['FighterAttack']['direction'])) {
				 // attaquer
				 $return = $this->Fighter->doAttack($Fid, $this->request->data['FighterAttack']['direction']);
			 }
		}
		$this->set('raw',$this->Fighter->find("all"));
		$this->setAction('tableau');

	}
	
	
	/**
	* diary method : journe de guerre
	*
	* @return void
	*/
	public function diary() {
		//récupérer tout les id
		$fighterID = $this->Session->read('current_fighter');
		$idTabToPrint = $this->Matche->getJournalDeGuerre($fighterID);
		if ($idTabToPrint == false) {
			$this->set('fighters', array());
			return;
		}
		$i = 0;
		$totalMatch = count($idTabToPrint)-1;
		
		$fighters = array();
		
		while ($totalMatch >= 0 ) {

			$photo1 = $this->Fighter->getUrlPhoto( $idTabToPrint[$i]['Matche']['fighter1_id']);
			$photo2 = $this->Fighter->getUrlPhoto( $idTabToPrint[$i]['Matche']['fighter2_id']);
			$date = date_create( $idTabToPrint[$i]['Matche']['time'] );
			if ($photo1 != null && $photo2 != null) {
				$fighter = array($this->Fighter->getName( $idTabToPrint[$i]['Matche']['fighter1_id']),
								$this->Fighter->getName( $idTabToPrint[$i]['Matche']['fighter2_id']),
								$this->Fighter->getLevel( $idTabToPrint[$i]['Matche']['fighter1_id']),
								$this->Fighter->getLevel( $idTabToPrint[$i]['Matche']['fighter2_id']),
								$photo1,
								$photo2,
								date_format($date, 'd/m/y'),
								$idTabToPrint[$i]['Matche']['status'] );
			} else if($photo1 == null && $photo2 != null) {
				$fighter = array($this->Fighter->getName( $idTabToPrint[$i]['Matche']['fighter1_id']),
								$this->Fighter->getName( $idTabToPrint[$i]['Matche']['fighter2_id']),
								$this->Fighter->getLevel( $idTabToPrint[$i]['Matche']['fighter1_id']),
								$this->Fighter->getLevel( $idTabToPrint[$i]['Matche']['fighter2_id']),
								'noPhoto',
								$photo2,
								date_format($date, 'd/m/y'),
								$idTabToPrint[$i]['Matche']['status'] );

			} else if ($photo1 != null && $photo2 == null) {
				$fighter = array($this->Fighter->getName( $idTabToPrint[$i]['Matche']['fighter1_id']),
								$this->Fighter->getName( $idTabToPrint[$i]['Matche']['fighter2_id']),
								$this->Fighter->getLevel( $idTabToPrint[$i]['Matche']['fighter1_id']),
								$this->Fighter->getLevel( $idTabToPrint[$i]['Matche']['fighter2_id']),
								$photo1,
								'noPhoto',
								date_format($date, 'd/m/y'),
								$idTabToPrint[$i]['Matche']['status'] );
			} else {
				$fighter = array($this->Fighter->getName( $idTabToPrint[$i]['Matche']['fighter1_id']),
								$this->Fighter->getName( $idTabToPrint[$i]['Matche']['fighter2_id']),
								$this->Fighter->getLevel( $idTabToPrint[$i]['Matche']['fighter1_id']),
								$this->Fighter->getLevel( $idTabToPrint[$i]['Matche']['fighter2_id']),
								'noPhoto',
								'noPhoto',
								date_format($date, 'd/m/y'),
								$idTabToPrint[$i]['Matche']['status'] );
			}
			array_push($fighters, $fighter);
			
			++$i;
			--$totalMatch;
		}
		$this->set('fighters', $fighters);
		$this->set('kd', $this->Matche->getKDStats($fighterID));
	}
	
	
	/**
	* onLineFighter method : check les check fighter on line
	*
	* @return void
	*/
	public function onLineFighter () {
		$fightersOnLine = $this->Fighter->getFighterOnLine($this->Session->read('current_fighter'));
		$this->set('fightersOnLine', $fightersOnLine);
		$i = 0;
		$totalMatch = count($fightersOnLine)-1;
		
		$fighters = array();
		
		while ($totalMatch >= 0 ) {

			$photo = $fightersOnLine[$i]['Fighter']['avatar_url'];
			if ($photo != null ) {
				$fighter = array($fightersOnLine[$i]['Fighter']['name'],
								$fightersOnLine[$i]['Fighter']['level'],
								$photo,
								$this->Guild->getGuildName($fightersOnLine[$i]['Fighter']['guild_id']),
								$this->Fighter->getArenaNamOf($fightersOnLine[$i]['Fighter']['id']) );
			} else {
				$fighter = array($fightersOnLine[$i]['Fighter']['name'],
								$fightersOnLine[$i]['Fighter']['level'],
								'noPhoto',
								$this->Guild->getGuildName($fightersOnLine[$i]['Fighter']['guild_id']),
								$this->Fighter->getArenaNamOf($fightersOnLine[$i]['Fighter']['id']) );
			}
			array_push($fighters, $fighter);

			++$i;
			--$totalMatch;
		}
		$this->set('fighters', $fighters);

	}

	/**
	* edit method :
	*
	* @return void
	*/
	public function edit() {
		$fighterId = $this->Session->read('current_fighter');
		
		//if ($this->request->is('post')) {
			
			if ( isset($this->request->data['FighterChooseAvatar']['avatar_file']) ) {
				//var_dump($this->request->data);
				// upload avatar
				$return = $this->Fighter->doUploadAvatar($fighterId, $this->request->data['FighterChooseAvatar']['avatar_file']);
			}
			$this->set('raw',$this->Fighter->find("all"));
		//}
	

		
		if( $this->request->is('ajax') ) {
			//Ajax pour actualisation stats
			$actu = intval(htmlspecialchars($this->request->data('actualise')));
			if($actu == 1){
				$data = $this->Fighter->getFighterStatsWithStuff($fighterId);
				print_r(json_encode($data));
				die;
			} 

			//Ajax pour les boutons de skill
			$skill = htmlspecialchars($this->request->data('skill'));
			if($skill){
				$this->Fighter->upSkill($skill, $fighterId);
				die;
			}
			//Ajax suppression item
			$delete_tool = intval(htmlspecialchars($this->request->data('delete_tool')));
			if($delete_tool>0){
				$this->Inventory->deleteItemInventory($fighterId,$delete_tool);
				die;
			}

			//Ajax pour la sauvegarde item sauvegardé
			$toolsId = array();
			for ($i=0; $i < 10; $i++) { 
				if($this->request->data($i) !== null)
					$toolsId[] = intval(htmlspecialchars($this->request->data($i)));
			}
			$this->Fighter->unequipStuff($fighterId);
			if(count($toolsId) > 1)
				$this->Inventory->equipItems($fighterId,$toolsId);
			elseif(count($toolsId) == 1)
				$this->Inventory->equipItems($fighterId,$toolsId[0]);
			else
				$this->Inventory->equipItems($fighterId,0);
			$this->Fighter->equipStuff($fighterId);
			die;
		}
		else {
			$stuffInventory = $this->Fighter->getStuffInventory($fighterId);
			$fightersStatsEquiped = $this->Fighter->getFighterStatsWithStuff($fighterId);
			$stuffEquipped = $this->Fighter->getStuffEquipped($fighterId);
			$fighterStats = $this->Fighter->getFighterStats($fighterId);
			$itemSlot = array('weapon', 'armor');
			
			$fighterStuff = array();
			foreach ($stuffEquipped as $tool) {
				$fighterStuff[$tool['tools']['placement']] = $tool['tools'];
			}
			//print_r($fightersStatsEquiped);
			//die;
			$this->set('fighterStats', $fighterStats);
			$this->set('fighterStuff',$fighterStuff);
			$this->set('stuffInventory', $stuffInventory);
			$this->set('fightersStatsEquiped', $fightersStatsEquiped);
			$this->set('stuffEquipped', $stuffEquipped);
			$this->set('itemSlot',$itemSlot);
		}
		
	}
	
	public function myFighters() {
		$playerID = $this->Auth->User('id');
		$fighters = $this->Fighter->getMyFighter($playerID);
		if( $this->request->is('ajax') ) {
			$fighterId = intval(htmlspecialchars($this->request->data('fighter')));
			$test = 0;
			$fighterName="";
			foreach ($fighters as $fighter) {
				if ( $fighter['Fighter']['id'] == $fighterId){
					$test = 1;
					$fighterName = $fighter['Fighter']['name'];
				}
			}
			if($test){
				$this->Session->write('current_fighter', $fighterId);
				$this->Session->write('current_fighter_name', $fighterName);
				print_r($this->Session->read('current_fighter'));
			}
			die;
		}
		else {
			if($this->request->is('post')) {
				if(isset($this->request->data['delete'])) {
					//pr($this->request->data['delete']['X']);
					$this->Fighter->deleteFighter($this->request->data['delete']['X'], $playerID);
					$fighters = $this->Fighter->getMyFighter($playerID);
					if($fighters) {
						$this->Session->write('current_fighter', $fighters['0']['Fighter']['id']);
					}
				}
			}
			if($fighters) {
				$i = 0;
				$totalMatch = count($fighters);
				while ($i < $totalMatch) {
					if($fighters[$i]['Fighter']['guild_id'] != NULL) {
						$fighters[$i]['Fighter']['guild_id'] = $this->Guild->getGuildName($fighters[$i]['Fighter']['guild_id']);
					}
					else $fighters[$i]['Fighter']['guild_id'] = '<em style="color:grey">Aucune Guilde</em>';
					if($fighters[$i]['Fighter']['avatar_url'] == NULL) {
						 $fighters[$i]['Fighter']['avatar_url'] = 'noPhoto';
					}
					$fighters[$i]['Fighter']['xp'] = $fighters[$i]['Fighter']['xp'];
					$fightersID[$i]=$fighters[$i]['Fighter']['id'];
					$i++;
				}
			}
			$this->set('fighters', $fighters);
			if($this->Session->read('current_fighter') != null)
				$this->set('fighterId', $this->Session->read('current_fighter'));
			else
				$this->set('fighterId', '0');
		}
	}
	
	
	/**
	* guild method : gere la creation / find et affichage de la guilde
	*
	* @return void
	*/
	public function guild() {
		$fighterID = $this->Session->read('current_fighter');
		$this->loadModel('Fighter');
		//$fighterID = 12;
		$i=0;
		$j=0;
		$moy=0;
		$id=0;
		if ($this->request->is('post')) {
			if (isset($this->request->data['CreatGuilde']['nom']) && $this->request->data['CreatGuilde']['nom']!='') {
				$id=$this->Guild->creationGuild(htmlspecialchars($this->request->data['CreatGuilde']['nom']));
				$this->set('achanger', 1);
				//$fighte=$this->Fighter->find('first',array('conditions' => array('Fighter.id' => $fighterID)));
				//$fighte['guild_id']=$id;
				$fighte = array('id' => $fighterID, 'guild_id' => $id);
				$this->Fighter->save($fighte);
			}
			elseif (isset($this->request->data['Join']['Rejoindre'])) {
				$fighte = array('id' => $fighterID, 'guild_id' =>htmlspecialchars($this->request->data['Join']['Rejoindre']));
				$this->Fighter->save($fighte);
			}
			elseif (isset($this->request->data['Leave']['Quitter'])) {
				$fighte = array('id' => $fighterID, 'guild_id' => NULL);
				$this->Fighter->save($fighte);
				if($this->Fighter->find('count', array('conditions' => array('guild_id'=>htmlspecialchars($this->request->data['Leave']['Quitter'])))) == 0) {
					$this->Guild->delete(htmlspecialchars($this->request->data['Leave']['Quitter']));
				}
			}
		}
		$guildeID = $this->Fighter->getGuild($fighterID);
		if ($guildeID == false) {
			$this->set('noGuilde', true);
			$guildeexist = $this->Guild->find('all');
			$number = $this->Guild->find('count')-1;
			while($i<=$number)
			{
				$guildeexist[$i]['Guild']['fighters'] = $this->Fighter->find('count', array('conditions' => array('guild_id' => $guildeexist[$i]['Guild']['id'])));
				$niveauperso=$this->Fighter->find('all', array('conditions' => array('guild_id'=>$guildeexist[$i]['Guild']['id'])));
				if($guildeexist[$i]['Guild']['fighters']!=0)
				{
					$moy=0;
					$j=0;
					while($j<=$guildeexist[$i]['Guild']['fighters']-1)
					{
						$moy=$moy+$niveauperso[$j]['Fighter']['level'];
						$j++;
					}
					$guildeexist[$i]['Guild']['levelmoy']=$moy/$guildeexist[$i]['Guild']['fighters'];
				}
				else {
					$guildeexist[$i]['Guild']['levelmoy']=0;
				}
				$i++;
			}
			$this->set('guildeexist', $guildeexist);
			$this->set('number', $number);
		}
		else {
			$this->set('noGuilde', false);
			$Guildname=$this->Guild->getGuildName($guildeID);
			$fighteringuild = $this->Fighter->find('all', array('conditions' => array('guild_id' => $guildeID) ));
			$countinguild = $this->Fighter->find('count', array('conditions' => array('guild_id' => $guildeID) ))-1;
			$this->set('fighteringuild', $fighteringuild);
			$this->set('countinguild', $countinguild);
			$this->set('guildid', $guildeID);
			$this->set('guildname', $Guildname);
		}
	}

	public function createperso() {
		$this->Session->write('current_fighter', '');
		$this->Session->write('current_fighter_name', '');
		if ($this->request->is('post')) {
			if (isset($this->request->data['creat']['nom'])) {
				$id = $this->Fighter->doCreate(htmlspecialchars($this->request->data['creat']['nom']), $this->Auth->User('id'));
				//var_dump($id);
				if($id === false)
				{
					$this->set('erreur', true);
				}
				else{
					$this->Inventory->newItemInventoryEquipped((int)$id, 7);
					$this->Inventory->newItemInventoryEquipped((int)$id, 12);
					$this->redirect('/arenas/myfighters');
				}
				
			}
		}
	}

	public function tableau() {
		$fighterID = $this->Session->read('current_fighter');
		$i=0;
		$j=0;
		$a=-15;
		$b=15;
		$c=-15;
		$d=15;
		$tab= array();
		for($i = $a ; $i <= $b ; $i++) {
			for($j = $c ; $j <= $d ; $j++) {
				$tab[$i][$j] = '0';
			}
		}
		$monfighter = $this->Fighter->find('first', array('conditions' => array('Fighter.id' => $fighterID))); 
		if(!(intval($monfighter['Fighter']['coordinate_x']) <= $a && intval($monfighter['Fighter']['coordinate_x']) >= $b && intval($monfighter['Fighter']['coordinate_y']) <= $c && intval($monfighter['Fighter']['coordinate_y']) >= $d)) {
		$tab[intval($monfighter['Fighter']['coordinate_x'])][intval($monfighter['Fighter']['coordinate_y'])]='H';
		$lesfighter = $this->Fighter->find('all');
		$count = count($lesfighter);
		for($i = $a ; $i <= $b ; $i++) {
			for($j = $c ; $j <= $d ; $j++) {
				if($i >= intval($monfighter['Fighter']['coordinate_x'] - 1 - $monfighter['Fighter']['skill_sight']) && $i <= intval($monfighter['Fighter']['coordinate_x'] + 1 + $monfighter['Fighter']['skill_sight'])) {
					if($j >= intval($monfighter['Fighter']['coordinate_y'] - 1 - $monfighter['Fighter']['skill_sight']) && $j <= intval($monfighter['Fighter']['coordinate_y'] + 1 + $monfighter['Fighter']['skill_sight'])) {
						if($tab[$i][$j] != 'H') {
							$tab[$i][$j] = 'V';
						}
					}
				}
			}
		}
		foreach ($lesfighter as $i) {
			if($tab[intval($i['Fighter']['coordinate_x'])][intval($i['Fighter']['coordinate_y'])] == 'V') {
				$tab[intval($i['Fighter']['coordinate_x'])][intval($i['Fighter']['coordinate_y'])] = 'X';
			}
		}
	}
	$this->set('carte', $tab);
	}

	public function accueil() {

	}

	public function messages() {
		if($this->request->data){
			$fighterName = htmlspecialchars($this->request->data['recherche']);
			$myData = $this->Fighter->find('all', array('conditions'=>array('Fighter.name' => $fighterName)));
			if(count($myData)){
				$this->set('fighterPerso', $myData);
			}
			else {
				$this->set('error', true);
				$this->set('errorName', $this->request->data['recherche']);
			}
		}

		$myMessage = new Message();
		$fightersId = $this->Session->read('current_fighter');
		$myFightersId = $myMessage->getallFighterId($fightersId);
		$myData = $this->Fighter->find('all', array(
			'conditions' => array('Fighter.id' => $myFightersId)
			));
		foreach ($myData as $key => $myFighter) {
			$myData[$key]['Fighter']['guild_name'] = $this->Guild->getGuildName($myFighter['Fighter']['guild_id']);
		}
		//print_r($myData);
		$this->set('fighters',$myData);
	}
}
?>