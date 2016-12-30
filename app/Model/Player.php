<?php

App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class Player extends AppModel {
	
	public $name = 'Player';
	
    public $validate = array(
        'email' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Email correct requis'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Un mot de passe est requis'
            )
        ),
    );
    


 /**
 * hashage mot de passe
 */
	public function beforeSave($options = array()) {
	    if (isset($this->data[$this->alias]['password'])) {
	        $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash($this->data[$this->alias]['password']);
	    }
	    return true;
	}
	
	public function sheckEmailEvalable($email) {
		$existeMail = $this->find('first', array('conditions' => array('email' => $email)));
	    if (!empty($existeMail) ) 
	    	return false;
	    
	    return true;
	}
	
	
	public function sendMailInscription ($email) {
		App::uses('CakeEmail', 'Network/Email');
		$mail = new CakeEmail();
		$mail->template('temp1');
		$mail->to($email);
		$mail->from('contact@monolithic.fr');
		$mail->subject('Mot de passe oublié');
		$mail->emailFormat('html');
		
		$mail->viewVars(array('fgPSW' => false));
		
			//a changer pour le mettre correctement
		return $mail->send();
	}
	

	public function send($dmail, $mdp){
		$player = $this->find('first', array('conditions' => array('email' => $dmail)));
		
		if ($player){
			App::uses('CakeEmail', 'Network/Email');
			$mail = new CakeEmail();
			$mail->template('temp1');
			$mail->to($player['Player']['email']);
			$mail->from('contact@monolithic.fr');
			$mail->subject('Mot de passe oublié');
			$mail->emailFormat('html');
			
			$mail->viewVars(array('fgPSW' => true, 'valueMDP' => $mdp, 'urlReInit' => "http://www.monolithic.fr/joueurs/reinit"));
			
				//a changer pour le mettre correctement
			return $mail->send();
		} else {
			return false;
		}
	}
	
	public function initNewPasswd ($mail, $code, $passwd) {
		
		$data =  $this->find('first', array('conditions' => array('email' => $mail )));
		
		$player = $this->read(null, $data['Player']['id']);
		
		if ($player['Player']['facebook_id'] == $code) {
			$passwordHasher = new BlowfishPasswordHasher();
			$this->set('password', $passwd );
			$this->set('facebook_id', 0 );
			$this->save();
			return false; // pas d'error
		}
		return  true; // error

	}

}

?>