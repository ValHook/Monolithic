<?php

App::uses('AppModel', 'Model');
App::uses('Tool', 'Model');

class Inventory extends AppModel {
	
	public $name = 'Inventory';

	public function equipItems($fighterId, $toolsId) {
		$tool = new Tool;
		$myData = $tool->find('all',array(
				'conditions' => array(
					'OR' => array(
						"Tool.id =" => $toolsId
						)
					),
				'fields' => array('COUNT(DISTINCT Tool.placement) as count')
			)
		);
		$this->updateAll(
			array('Inventory.equipped' => false),
			array('Inventory.fighter_id =' => $fighterId)
		);
		if (count($toolsId) == intval($myData[0][0]['count'])){
			if(count($toolsId) == 1) {
				$this->updateAll(
					array('Inventory.equipped'=> 1),
					array('Inventory.fighter_id =' =>$fighterId,
						'Inventory.tool_id =' => $toolsId)
					);
			}
			else {
				foreach ($toolsId as $tool_id) {
					$this->updateAll(
						array('Inventory.equipped'=> 1),
						array('Inventory.fighter_id =' =>$fighterId,
							'Inventory.tool_id =' => $tool_id)
						);
				}
			}
			print_r("Sauvegarde effectuée");
		}
		elseif($toolsId == 0) {
			print_r("Sauvegarde effectuée");
		}
		else{
			print_r("Don't try to cheat please");
		}
	}

	public function newItemInventory($fighterId, $toolId) {
			$this->create();
		    $data = array('Inventory' => 
 					array('equipped' => '0',
 						 'tool_id' => intval($toolId),
 						 'fighter_id' => intval($fighterId)));
		    $equippedItem = $this->save($data);
	}
	
	public function newItemInventoryEquipped($fighterId, $toolId) {
			$this->create();
		    $data = array('Inventory' => 
 					array('equipped' => '1',
 						 'tool_id' => intval($toolId),
 						 'fighter_id' => intval($fighterId)));
		    $equippedItem = $this->save($data);
	}

	public function deleteItemInventory($fighterId,$toolId) {
		$this->deleteAll(array('Inventory.fighter_id' => $fighterId, 
			'Inventory.tool_id' => $toolId));
	}
	
	/* Tentative de lootage */
	public function tryLoot($fid, $level) {
		$treshold = 86;
		$rand = rand(0,100);
		if ($rand < $treshold) {
			return "Aucun Loot";
		}
		$loot_table = $this->getItemsConsideringLevel($level);
		$current_items = $this->getItemsOfFighter($fid);
		$looted_item = $loot_table[rand(0,count($loot_table)-1)];
		$ok = true;
		foreach ($current_items as $i) {
			if ($i['Inventory']['tool_id'] == $looted_item['Tool']['id']) {
				$ok = false;
				break;
			}
		}
		if ($ok == true) {
			$this->newItemInventory($fid, $looted_item['Tool']['id']);
			return "Vous avez looté : ".strtoupper($looted_item['Tool']['Description_v'])."!";
		}
		return "Vous avez looté un item que vous possédez déjà";
		
	}
	public function getItemsConsideringLevel($level) {
		$tool = new Tool;
		$tools = $tool->find('all',array('conditions' => array('Tool.level <=' => $level)));
		return $tools;
	}
	public function getItemsOfFighter($fid) {
		$tools = $this->find('all',array('conditions' => array('Inventory.fighter_id =' => $fid)));
		return $tools;
	}

}

?>