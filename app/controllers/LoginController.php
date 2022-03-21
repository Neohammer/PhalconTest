<?php
/**
*	Login Controller
*
	Control connexion for administrator and readonly
*
**/

use Phalcon\Mvc\Controller;

class LoginController extends Controller
{
	
	/**
	*
	* Control login display
	*
	**/
    public function indexAction()
    {
		$this->assets->addCss('bootstrap.min.css');
		
		
    }
	
	/**
	*
	* Control login authentification
	*
	**/
	public function authenticateAction()
    {
		
		$user = new Users();

        //assign value from the form to $user
        $user->assign(
            $this->request->getPost(),
            [
                'name',
                'email'
            ]
        );

        // Store and check for errors
        $success = $user->save();

        // passing the result to the view
        $this->view->success = $success;

        if ($success) {
            $message = "Thanks for registering!";
        } else {
            $message = "Sorry, the following problems were generated:<br>"
                     . implode('<br>', $user->getMessages());
        }

        // passing a message to the view
        $this->view->message = $message;
    }
	
	/**
	*
	* Control login authentification
	*
	**/
	public function logoutAction()
	{
		
	}
}