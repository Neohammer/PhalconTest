<?php

use Phalcon\Mvc\Controller;

use Phalcon\Http\Response;

class IndexController extends Controller
{
	
	
	/**
	* Main access
	*
	**/
    public function indexAction()
    {
		
		if ($this->request->isPost()) {
			
			//get posted values
			$login = $this->request->getPost('login');
            $password = $this->request->getPost('password');
			
			//find first result from administrator
			$Administrator = Administrator::findFirst(
				[
					'conditions' => 'login = :login: AND password = :password:',
					'bind'       => [
						'login' => $login,
						'password' => md5($password),
					],
				]
			);
			
			//An account is found
			if( $Administrator )
			{
				$this->registerSession($Administrator);
				
				if( $Administrator->getName() ) $this->flash->success("Welcome ".$Administrator->getName());
				else $this->flash->success("Welcome");
				
				return (new Response())->redirect('client');
			}
				
				
			$this->flash->error("Login and password do not match.");
			return (new Response())->redirect();
			
			
		}
		
    }
	 /**
     * Register an authenticated user into session data
     *
     * @param Users $user
     */
    private function registerSession(Administrator $Administrator): void
    {
        $this->session->set('auth', [
            'id'   	=> $Administrator->getId(),
            'login' => $Administrator->getLogin(),
        ]);
    }
}