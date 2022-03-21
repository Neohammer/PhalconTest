<?php
/**
* Client management
*
* @author M.Marceau <ma.marceau@gmail.com>
*/

use Phalcon\Mvc\Controller;

use Phalcon\Http\Response;


/**
 * @RoutePrefix('/client')
 */
class ClientController extends Controller
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
		$this->assets->addJs('js/jquery.min.js');
		$this->assets->addJs('js/common_list.js');
	
		//associate clients to view
		$this->view->Clients = Client::find();
		
	}
	/**
     * @Get(
     *     '/edit/{id:[0-9]+}'
     * )
     * @Post(
     *     '/edit/{id:[0-9]+}'
     * )
     */
	public function editAction( $id ){
		
		if($response = $this->checkAccess('edit') )
		{
			return $response;
		}
		
		$Client = Client::findFirst( $id );
		
		if( !$Client )
		{
			$this->flash->error('This client does not exist.');
			return (new Response())->redirect('client');
			
		}
		
		if ($this->request->isPost()) {
			
			$Client->assign(
				$this->request->getPost(),
				[
					'name',
					'address',
					'country',
				]
			);
			
			$success = $Client->save();
			
			
			if(!$success)
			{
				$this->saveFormDatas();
				$messages = $Client->getMessages();
				foreach($messages as $message)
				{
					$this->flash->error($message);
				}
				return (new Response())->redirect('client/edit/'.$Client->getId());
			}
			$this->clearFormDatas();
			
			$this->flash->success("Client has been updated");
			return (new Response())->redirect('client');
			
		}
		
		
		$this->view->Client = $this->assignFormDatas($Client);
		
		///get products		
		$this->view->Products = Product::Find(["stock > 0"]);
		
		
    }
	/**
     * @Get(
     *     '/add'
     * )
     * @Post(
     *     '/add'
     * )
     */
	public function addAction()
	{
		
		if($response = $this->checkAccess('add') )
		{
			return $response;
		}
		
		if ($this->request->isPost()) {
			
			$Client = new Client();
			$Client->assign(
				$this->request->getPost(),
				[
					'name',
					'address',
					'country',
				]
			);
			
			$success = $Client->save();
			
			
			if(!$success)
			{
				$this->saveFormDatas();
				$messages = $Client->getMessages();
				foreach($messages as $message)
				{
					$this->flash->error($message);
				}
				return (new Response())->redirect('client/add');
			}
			$this->clearFormDatas();
			
			$this->flash->success("Client has been created");
			return (new Response())->redirect('client');
			
		}
		
		$Client = new Client();
		
		
		$this->view->Client = $this->assignFormDatas($Client) ;
		
		
	}
	
	
	/**
     * @Post(
     *     '/buy/{id:[0-9]+}'
     * )
	 * Will come later....
     */
	public function buyAction( $id ){
		
		if($response = $this->checkAccess('buy') )
		{
			return $response;
		}
		
		
			
		//check Client existence
		$Client = Client::findFirst( $id );
		
		if( !$Client )
		{
			$this->flash->error('This client does not exist.');
			return (new Response())->redirect('client');
			
		}
		
		//check Company existence
		$Company = Company::findFirst( $this->request->getPost('company_id'));
		
		if( !$Company )
		{
			$this->flash->error('This company does not exist.');
			return (new Response())->redirect('client/edit/'.$Client->getId());
		}
		
		//check Product existence
		$Product = Product::findFirst($this->request->getPost('product_id'));
		
		if( !$Product )
		{
			$this->flash->error('This product does not exist.');
			return (new Response())->redirect('client/edit/'.$Client->getId());
		}
		
		//check quantity
		$Quantity = (int)$this->request->getPost('quantity');
		
		
		if( !$Product->canRemoveFromStock( $Quantity ) )
		{
			$this->flash->error('Please check quantity.');
			return (new Response())->redirect('client/edit/'.$Client->getId());
		}
		
		$Product->removeFromStock( $Quantity );
		$Product->Save();
		
		//all is ok
		
		$Transaction = new Transactionclient();
		
		$Transaction->assign(
			$this->request->getPost(),
			[
				'company_id',
				'product_id',
				'quantity'
			]
		);
		$Transaction->setClientId( $Client->getId() );
		
		$Success = $Transaction->Save();
		
		
		if(!$success)
		{
			$this->saveFormDatas();
			$messages = $Client->getMessages();
			foreach($messages as $message)
			{
				$this->flash->error($message);
			}
			return (new Response())->redirect('client/edit/'.$Client->getId());
		}
	//	$this->clearFormDatas();
		
		
		
		$this->flash->success("Transaction has been created");
		
		
		return (new Response())->redirect('client/edit/'.$Client->getId());
		
    }
	
	
	
	/**
     * @Get(
     *     '/remove/{id:[0-9]+}'
     * )
     */
	public function removeAction( $id ){
		
		if($response = $this->checkAccess('remove') )
		{
			return $response;
		}
		
		$Client = Client::findFirst( $id );
		
		if( !$Client )
		{
			$this->flash->error('This client does not exist.');
			
		}
		elseif( $Client->delete() )
		{
			$this->flash->warning('This client has been removed.');
		}
		else
		{
			$this->flash->error('An error has occured.');
		}
		return (new Response())->redirect('client');
		
    }
	/** save Datas from post form form error
     *
     */
	private $formDatas = 'tmp_client_form';
	private function saveFormDatas()
	{
		$this->session->set($this->formDatas , $this->request->getPost());
		
	}
	 /**
     * clear Datas from post
     *
     */
	private function clearFormDatas()
	{
		if( $this->hasFormDatas() )
			$this->session->remove($this->formDatas );
		
	}
	 /**
     * check if Datas from post
     *
     */
	private function hasFormDatas()
	{
		return $this->session->has($this->formDatas );
		
	}
	
	 /**
     * assign datas from form to model
     *
     */
	private function assignFormDatas( $Model )
	{
		if ($this->hasFormDatas($this->formDatas ) )
		{
			$Model->assign( 
				$this->session->get($this->formDatas),
				
				[
					'name',
					'address',
					'country'
				]
				
			);
			$this->clearFormDatas();
		}
		return $Model;
	}
}