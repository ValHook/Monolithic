<?php

App::uses('AppModel', 'Model');

class Message extends AppModel {

 /**
 * @param $fighter_id_from id du personnage qui envoie un message 
 * @param $message Fighter attaquant
 * @param $fighter_id , si message perso alors recoit l'id du personnage a qui envoyer le message
 * @return true si pas d'erreur
 * @return $error si il y a eu une erreur
 * @todo Tester tous les champs pour éviter les données erronées: fighter_id_from, fighter_id, title, message
 */
 	function addMessage($fighter_id_from, $message, $fighter_id_to, $guild_id = null) {
 		
 		$data = array('Message' => 
 					array('date' => date('Y-m-d H:i:s'),
 						 'message' => $message,
 						 'fighter_id_from' => $fighter_id_from));
 		if(isset($guild_id))
 			$data['Message']['guild_id'] = $guild_id;
 		if($fighter_id_to)
 			$data['Message']['fighter_id_to'] = $fighter_id_to;
 		$message = $this->save($data);

 		return $message['Message']['id'];
 	}

 /**
 * @param $lastId id du dernier message 
 * @return $myData l'ensemble des données brutes recues
 */
 	function getData($lastId, $myFighterId, $fighter_id_to, $guild_id = null) {
 		//$lastId = 185;
		$joins = array(
			  array(
			    'table' => 'fighters',
			    'alias' => 'fighters',
			    'conditions' => array('Message.fighter_id_from = fighters.id')
			  )
			);
		if($lastId){
			$lastMessage = $this->find('all', array(
				'conditions' => array(
					'Message.id =' => $lastId),
				'fields' => array('Message.date')));

			$lastDate = $lastMessage[0]['Message']['date'];

			$requete = array(
			'conditions' => array(
				'Message.date >' => $lastDate,
				'Message.id >' => $lastId),
			'joins' => $joins,
			'fields' => array('Message.id', 'UNIX_TIMESTAMP(Message.date)', 'Message.message', 'fighters.name','fighters.avatar_url'));
			
			isset($guild_id) ?
				$requete['conditions']['Message.guild_id ='] = $guild_id :
				$requete['conditions']['Message.guild_id'] = NULL;

			if($fighter_id_to){
				if($fighter_id_to != $myFighterId) {
					$requete['conditions']['OR'] = array(
						array('Message.fighter_id_to =' => $fighter_id_to,
							'Message.fighter_id_from =' => $myFighterId),
						array('Message.fighter_id_to =' => $myFighterId,
							'Message.fighter_id_from =' => $fighter_id_to)
					 );
					$requete['conditions']['Message.fighter_id_to !='] = 'Message.fighter_id_from';
				}
				else {
					$requete['conditions']['Message.fighter_id_to ='] = $fighter_id_to;
					$requete['conditions']['Message.fighter_id_from ='] = $myFighterId;	
				}
			}
			else {
				$requete['conditions']['Message.fighter_id_to ='] = $fighter_id_to;
			}

			$myData = $this->find('all', $requete);

		}
		else{
			$five_minute_interval = 60*60;
			if($fighter_id_to)$five_minute_interval = 60*120;
			$lastDate = date('Y-m-d H:i:s', time()-$five_minute_interval);
			$requete = array(
			'conditions' => array(
				'Message.date >' => $lastDate),
			'joins' => $joins,
			'fields' => array('Message.id', 'UNIX_TIMESTAMP(Message.date)', 'Message.message', 'fighters.name','fighters.avatar_url'));
			
			if($fighter_id_to){
				if($fighter_id_to != $myFighterId) {
					$requete['conditions']['OR'] = array(
						array('Message.fighter_id_to =' => $fighter_id_to,
							'Message.fighter_id_from =' => $myFighterId),
						array('Message.fighter_id_to =' => $myFighterId,
							'Message.fighter_id_from =' => $fighter_id_to)
					 );
					$requete['conditions']['Message.fighter_id_to !='] = 'Message.fighter_id_from';
				}
				else {
					$requete['conditions']['Message.fighter_id_to ='] = $fighter_id_to;
					$requete['conditions']['Message.fighter_id_from ='] = $myFighterId;	
				}
			}
			else {
				$requete['conditions']['Message.fighter_id_to ='] = $fighter_id_to;
			}

			isset($guild_id) ?
				$requete['conditions']['Message.guild_id ='] = $guild_id :
				$requete['conditions']['Message.guild_id'] = NULL;

			$myData = $this->find('all', $requete);
		}
		return $myData;
 	}

/**
 * @param $myData ensemble de données brutes
 * @return $myReturn un tableau simplifié contenant uniquement les informations pour la vue
 */
 	function convertData($myData){
 		$myReturn = array();
 		foreach ($myData as $message) {
 			$tmp = array();

 			foreach ($message['Message'] as $key => $value) {
 				switch($key){
 					case 'guild_id':
 						if($value){
 							$tmp[$key] = $value;
 						}
 						break;
 					case 'id':
 					case 'message':
 					case 'fighter_id_from':
 						$tmp[$key] = $value;
 						break;
 				}
 			}
 			foreach ($message['fighters'] as $key => $value) {
 				switch($key){
 					case 'name':
 					case 'avatar_url':
 						$tmp[$key] = $value;
 						break;
 				}
 			}
 			$tmp['date'] = $message['0']['UNIX_TIMESTAMP(`Message`.`date`)'];
 			$myReturn[] = $tmp;
 		}
 		return $myReturn;
 	}


 	function getallFighterId($fighterId) {
		$myMessages = $this->find('all', array(
			'conditions' => array(
				'OR' => array(
					'Message.fighter_id_to' => $fighterId,
					'Message.fighter_id_from' => $fighterId
					)
				),
			'fields' => array('DISTINCT Message.fighter_id_to', 'Message.fighter_id_from')
			));
		
		$fightersId = array();
		foreach ($myMessages as $message) {
			if($message['Message']['fighter_id_from'] == $fighterId && $message['Message']['fighter_id_to']) {
				if(!in_array($message['Message']['fighter_id_to'], $fightersId)){
					$fightersId[] = $message['Message']['fighter_id_to'];
				}
			}else if($message['Message']['fighter_id_to'] && $message['Message']['fighter_id_from'] == $fighterId) {
				if(!in_array($message['Message']['fighter_id_from'], $fightersId)){
					$fightersId[] = $message['Message']['fighter_id_from'];
				}
			}
		}
		return $fightersId;
	}
}
?>