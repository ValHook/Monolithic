<?php
	App::uses('CakeSession', 'Model/Datasource');
	App::uses('BaseAuthenticate', 'Controller/Component/Auth');
	
	class FacebookAuthenticate extends BaseAuthenticate {

		var $settings = array(
		   "app_id" => "771486936314002",
		   "app_secret" => "a022fd58ce076166dc2ead12f380394d",
		   "url" => "http://www.monolithic.fr/joueurs/connexion"
		); 
		
        public function authenticate(CakeRequest $request, CakeResponse $response) {
           $session = new CakeSession();

            if (isset($request->query) && isset($request->query['code']) && isset($request->query['state'])) {

                    $token_url = "https://graph.facebook.com/oauth/access_token?"
                        . "client_id=" . $this->settings["app_id"]
                        . "&redirect_uri=" . urlencode($this->settings["url"])
                           . "&client_secret=" . $this->settings["app_secret"]
                           . "&code=" . $request->query['code'];
                         
                    $response = file_get_contents($token_url);
                    $params = null;
                    parse_str($response, $params);

                    if (isset($params['access_token'])) {
                        $graph_url = "https://graph.facebook.com/me?access_token=".$params['access_token'];
                         $fb_user = json_decode(file_get_contents($graph_url));
                         App::uses('Player', 'Model');
                        $User = new Player();
                        $user = $User->find("first", array("conditions" => array("facebook_id" => $fb_user->id)));
                        if (!$user) {
                            $user = array(
                                "Player" => array(
                                    "facebook_id" => $fb_user->id,
                                    "email" => $fb_user->id
                                )
                            );
                            $User->create();
                            $User->save($user);
                            $user["Player"]["id"] = $User->getLastInsertID();
                        }
                        return $user["Player"];
                    }
            }    
            return false;        
        }
    	
	}
?>