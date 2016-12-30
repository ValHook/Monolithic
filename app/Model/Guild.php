  <?php

App::uses('AppModel', 'Model');

class Guild extends AppModel {
	
  /**
 * @param $guildId a qui on veux trouver son nom
 * @return le non de la guilde
 * @return false si non
 */
 	function getGuildName($guildId) {
	 	//récupérer tout les id
		$name = $this->find('first', array('conditions' => array('id' => $guildId) ));
		if ($name != null) {
			return $name['Guild']['name'];
		}
		return false;
 	}

 	/*création d'une guilde*/
 	function creationGuild($guildname) {
 		$i=$this->find('count');
 		$a=0;
 		if($this->find('count', array('conditions' => array('name'=>$guildname)))==0) {
 			$this->create();
			$this->set('name', $guildname);
			while($a==0)
			{
				if($this->find('count', array('conditions' => array('id'=>$i)))==0)
				{
					$a=1;
					$this->set('id', $i);
				}
				else {
					$i++;
				}
			}
			$this->save();
		}
		return $i;
 	}
 }
 ?>