  <?php

App::uses('AppModel', 'Model');

class Matche extends AppModel {
	
  /**
 * @param $fighterId fighter a qui on affiche ses matches
 * @return les id de Matches a afficher
 * @return false si error ou 0
 */
 	function getJournalDeGuerre($fighterId) {
		$attaquant = $this->find('all', array('order' => 'time DESC', 'limit' => 20, 'conditions' => array(
														  'OR' => array(
														  		  array( 'fighter1_id' => $fighterId),
														  		  array( 'fighter2_id' => $fighterId)
														  		  )
											  )
								 ));
		if ($attaquant != null) {
			return $attaquant;
		}
		return false;
 	}

 	function getJournalDeGuerreAllTime($fighterId) {
		$attaquant = $this->find('all', array('order' => 'time DESC','conditions' => array(
														  'OR' => array(
														  		  array( 'fighter1_id' => $fighterId),
														  		  array( 'fighter2_id' => $fighterId)
														  		  )
											  )
								 ));
		if ($attaquant != null) {
			return $attaquant;
		}
		return false;
 	}

 	function getKDStats($fid) {
 		$all = $this->getJournalDeGuerreAllTime($fid);
 		$recent = $this->getJournalDeGuerre($fid);
 		$krecent = 0;
 		$drecent = 0;
 		$kall = 0;
 		$dall = 0;
 		foreach ($recent as $r) {
 			if ($r['Matche']['fighter1_id'] == $fid)
 				$krecent++;
 			else
 				$drecent++;
 		}
 		foreach ($all as $r) {
 			if ($r['Matche']['fighter1_id'] == $fid)
 				$kall++;
 			else
 				$dall++;
 		}
 		$drecent = $drecent ? $drecent : 1;
 		$dall = $dall ? $dall : 1;
 		$kdr = round($krecent/$drecent,2);
 		$kda = round($kall/$dall,2);
 		return array("kall" => $kall, "dall" => $dall, "krecent" => $krecent, "drecent" => $drecent, "kdr" => $kdr, "kda" => $kda);
 	}
 	
 	function logMatch($fid,$tid) {
	 	//INSERT INTO matche (id,fighter1_id, fighter2_id, date) VALUES (NULL,$fid, $tid, F1_WON,NOW())
	 	$this->create();
	 	$this->set('fighter1_id', $fid);
	 	$this->set('fighter2_id', $tid);
	 	$this->set('status', "F1_WON");
	 	$this->set('time', $this->getDataSource()->expression("NOW()"));
	 	$this->save();
 	}
 	
 }
 ?>