<?php
/**
* TransactionController management
*
* @author M.Marceau <m.marceau@gmail.com>
*/

use Phalcon\Mvc\Controller;

use Phalcon\Http\Response;


/**
 * @RoutePrefix('/transaction')
 */
class TransactionController extends Controller
{
	
	
	/**
	* Check if rights are correct
	*/
	public function checkAccess( $type )
	{
		$auth = $this->session->get('auth');
		if( !$auth )
		{
			$this->flash->error('You have to be log in to view this page.');
			return (new Response())->redirect();
		}
		$this->Administrator = Administrator::findFirst($auth['id']);
		
		if(  $type != 'list' && $this->Administrator->getReadOnly() )
		{
			$this->flash->error('You do not havec access to this page.');
			return (new Response())->redirect();
		}
		
		//expose administrateur to view 
		$this->view->Administrator = $this->Administrator;
	}
	
	/**
     * @Get(
     *     '/'
     * )
     */
    public function indexAction()
    {
		if($response = $this->checkAccess('list') )
		{
			return $response;
		}
		
		//inserting required assets
		$this->assets->addCss('css/fontawesome5.min.css');
		
	}
	
}