<?php
	session_start();
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
		
	public $components = array(
		'DebugKit.Toolbar',
		'RequestHandler',
        'Flash',
        'Session',
        'Auth' => array(
	        'loginAction' => array(
	            'controller' => 'players',
	            'action' => 'connexion',
	        ),
		    'authenticate' => array(
		        'Form' => array(
		                'fields' => array(
		                    'username' => 'email', // 'username' par défaut
		                    'password' => 'password'  // 'password' par défaut
		                ),
		                'passwordHasher' => 'Blowfish',
		                'userModel' => 'Player', 
		            ),
			     'Facebook' => array (
					'userModel' => 'Player'
				)
			)
		)
    );

    public function beforeFilter() {
	    
        $this->Auth->allow('display');

        $this->Auth->allow('index', 'view');
        if ($this->Auth->User('id') != null && $this->Session->read('current_fighter') == null) {
        	if (strpos($this->here,"myfighters") === FALSE && strpos($this->here,"createperso") === FALSE) {
        		$this->redirect('/arenas/myfighters');	
        	}
        }
        //Pas de theme si Ajax => afficher que le content
        if ($this->RequestHandler->isAjax()) {
	        $this->layout=null;
        }
    }
	
	function _setErrorLayout() {
		if ($this->name == 'CakeError')
		{
			$this->redirect('/');
		}
	}
 
        // On effectue un test avant de rendre la vue
	function beforeRender () {
		$this->_setErrorLayout();
	}
}
