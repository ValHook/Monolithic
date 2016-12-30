<?php $this->assign('titlePage', 'WEB ARENA');?>
 <?php 
echo $this->Form->create('FighterCreate');
echo $this->Form->input('name',array('label' => 'Name'));
echo $this->Form->end('Create');

echo $this->Form->create('FighterLvlUp');
echo $this->Form->input('fighterId',array('options' => array('1'=>'1','2'=>'2','3'=>'3')));
echo $this->Form->end('Lvl Up');

echo $this->Form->create('FighterChooseAvatar', array('type'=>'file'));
echo $this->Form->input('avatar_file',array('label' => 'Votre avatar (jpg ou png)', 'type' => 'file'));
echo $this->Form->end('Upload');
 
 ?>