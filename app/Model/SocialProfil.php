<?php
App::uses('AuthComponent', 'Controller/Component');
 
class SocialProfile extends AppModel {
     
    public $belongsTo = 'Player';
 
}

?>