<?php
App::uses('AppController', 'Controller');
App::uses('Validation', 'Utility');

class PlayersController extends AppController {
	
	var $uses = array('Player');

    public function beforeFilter() {
        parent::beforeFilter();
        // Permet aux utilisateurs d'accéder aux pages d'inscription et de déconnexion même lorsqu'ils ne sont pas connectés
        $this->Auth->allow('inscription', 'logout', 'forgot', 'reinit', 'default');
    }

    public function beforeRender() {
        $this->layout = MINIMALIST_LAYOUT;
    }


/*
* Fonctions relatives à la connexion
*/
    public function connexion() {
	    
	  if ($this->request->is('post') || $this->request->is('get')) {

        // facebook requests a csrf protection token
        if (!($csrf_token = $this->Session->read("state"))) {
            $csrf_token = md5(uniqid(rand(), TRUE));
            $this->Session->write("state",$csrf_token); //CSRF protection
        }
        $this->set("csrfToken",$csrf_token);
        
         // login        
        if ($this->Auth->login()) {
            return $this->redirect(array('controller' => 'Arenas', 'action' => 'accueil'));
        } 
	    
        if (!$this->Auth->loggedIn()) {
            if ($this->request->is('post')) {
                if ($this->Auth->login()) {
                    return $this->redirect(array('controller' => 'Arenas', 'action' => 'accueil'));
                } else {
                    $this->Flash->error(__("Email ou mot de passe invalide, réessayer"));
                }
            }
        }
        else {
            return $this->redirect('/arenes/myfighters');
        }
	  }  
	}


	public function logout() {
        $this->Session->delete('current_fighter');
        $this->Session->delete('current_fighter_name');
	    return $this->redirect($this->Auth->logout());
	}

    public function index() {
        $this->Player->recursive = 0;
        $this->set('players', $this->paginate());
    }

    public function view($id = null) {
        if (!$this->Player->exists($id)) {
            throw new NotFoundException(__('Player invalide'));
        }
        $this->set('players', $this->Player->findById($id));
    }

/*
* créer un nouveux player
* @todo gestion error mail deja entré
* @todo gestion fausse adresse mail
*/
    public function inscription() {
        if ($this->request->is('post')) {
	        $isEvalable = $this->Player->sheckEmailEvalable($this->request->data['Player']['email']);
	        if ($isEvalable) {
		        $this->Player->create();
	            if ($this->Player->save($this->request->data)) {
	                $this->Player->sendMailInscription($this->request->data['Player']['email']);
	                return $this->redirect(array('action' => 'connexion'));
	            } else {
	                $this->Flash->error(__('Le Player n\'a pas été sauvegardé. Merci de réessayer.'));
	            }
	        } else {
		        $this->Flash->error(__('Cette email est déjà utilisé. Merci de réessayer.'));
	        }
            
        }
    }

    public function edit($id = null) {
        $this->Player->id = $id;
        if (!$this->Player->exists()) {
            throw new NotFoundException(__('Player Invalide'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Player->save($this->request->data)) {
                $this->Flash->success(__('Le Player a été sauvegardé'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Le Player n\'a pas été sauvegardé. Merci de réessayer.'));
            }
        } else {
            $this->request->data = $this->Player->findById($id);
            unset($this->request->data['Player']['password']);
        }
    }

    public function delete($id = null) {
        // Avant 2.5, utilisez
        // $this->request->onlyAllow('post');

        $this->request->allowMethod('post');

        $this->Player->id = $id;
        if (!$this->Player->exists()) {
            throw new NotFoundException(__('Player invalide'));
        }
        if ($this->Player->delete()) {
            $this->Flash->success(__('Player supprimé'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Flash->error(__('Le Player n\'a pas été supprimé'));
        return $this->redirect(array('action' => 'index'));
    }

    public function forgot() {
    	$mdp=rand(0,10000000000);
        if ($this->request->is('post')) { //si je recois des données
            //debug($this->request->data);
            if ( Validation::email($this->request->data['Oubli']['email'])) { //l'email saisie est valide	     	                
	                
                   if ($this->Player->send($this->request->data['Oubli']['email'], $mdp)) {
                   		$this->Flash->success(__("Un mail vous a été envoyé afin de reconfigurer votre mot de passe"));
                   		$data = $this->Player->find('first', array('conditions' => array('email' => htmlspecialchars($this->request->data['Oubli']['email']))));
                   		$player = $this->Player->read(null, $data['Player']['id']);
                   		$this->Player->set('facebook_id', $mdp );
                   		$this->Player->save();
                   } else  {
	                    $this->Flash->error(__("Ce mail n'existe pas"));
                }
            }
            else { //si l'adresse n'est pas valide
                $this->Flash->error(__("Veuillez vous assurer d'entrer un email valide"));
            }
        }
    }



    public function reinit() {
        if ($this->request->is('post')) {
            if(isset($this->request->data['reinitialisation']['email']) && 
               isset($this->request->data['reinitialisation']['code']) && 
               isset($this->request->data['reinitialisation']['password'])) {
			   		$err = $this->Player->initNewPasswd(htmlspecialchars($this->request->data['reinitialisation']['email']),
			   											htmlspecialchars($this->request->data['reinitialisation']['code']),
			   											htmlspecialchars($this->request->data['reinitialisation']['password']));
			   		if (!$err) {
				   		return $this->redirect('/joueurs/connexion');
			   		} else {
				   		$this->Flash->error(__("Email ou code incorrect"));
			   		}
			   			
	        }
        }
    }
}
?>