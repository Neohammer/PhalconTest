<?php
/**
* Provider management
*
* @author M.Marceau <m.marceau@gmail.com>
*/

use Phalcon\Mvc\Controller;

use Phalcon\Http\Response;


/**
 * @RoutePrefix('/provider')
 */
class ProviderController extends Controller
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
	
		//associate providers to view
		$this->view->Providers = Provider::find();
		
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
		
		$Provider = Provider::findFirst( $id );
		
		if( !$Provider )
		{
			$this->flash->error('This provider does not exist.');
			return (new Response())->redirect('provider');
			
		}
		
		if ($this->request->isPost()) {
			
			$Provider->assign(
				$this->request->getPost(),
				[
					'name',
					'address',
					'country',
				]
			);
			
			$success = $Provider->save();
			
			
			if(!$success)
			{
				$messages = $Provider->getMessages();
				foreach($messages as $message)
				{
					$this->flash->error($message);
				}
				return (new Response())->redirect('provider/edit/'.$Provider->getId());
			}
			
			$this->flash->success("Provider has been updated");
			return (new Response())->redirect('provider');
			
		}
		
		//load assets
		$this->assets->addCss('css/fontawesome5.min.css');
		
		//load provider
		$this->view->Provider = $this->assignFormDatas($Provider);
		///get products		
		$this->view->Products = Product::Find();
		
		
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
			
			$Provider = new Provider();
			$Provider->assign(
				$this->request->getPost(),
				[
					'name',
					'address',
					'country',
				]
			);
			
			$success = $Provider->save();
			
			
			if(!$success)
			{
				$this->saveFormDatas();
				$messages = $Provider->getMessages();
				foreach($messages as $message)
				{
					$this->flash->error($message);
				}
				return (new Response())->redirect('provider/add');
			}
			
			$this->clearFormDatas();
			
			$this->flash->success("Provider has been created");
			return (new Response())->redirect('provider');
			
		}
		
		$Provider = new Provider();
		
		$this->view->Provider = $this->assignFormDatas($Provider) ;
		
		
	}
	/**
     * @Get(
     *     '/buy/{id:[0-9]+}'
     * )
     */
	public function buyAction( $id ){
		
		if($response = $this->checkAccess('buy') )
		{
			return $response;
		}
		
		//check Provider existence
		$Provider = Provider::findFirst( $id );
		
		if( !$Provider )
		{
			$this->flash->error('This provider does not exist.');
			return (new Response())->redirect('provider');
		}
		
		//check Product existence
		$Product = Product::findFirst($this->request->getPost('product_id'));
		
		if( !$Product )
		{
			$this->flash->error('This product does not exist.');
			return (new Response())->redirect('provider/edit/'.$Provider->getId());
		}
		
		//check quantity is more than 0
		$Quantity = (int)$this->request->getPost('quantity');
		
		if( !$Quantity || $Quantity < 0 )
		{
			$this->flash->error('You have to choose a product quantity.');
			return (new Response())->redirect('provider/edit/'.$Provider->getId());
		}
		
		//enough quantity
		if($Quantity > $Product->getQuantity())
		{
			$this->flash->error('Not enough product available.');
			return (new Response())->redirect('provider/edit/'.$Provider->getId());
		}
		
		//all is ok
		
		$Transaction = new Transactionprovider();
		
		$Transaction->assign(
			$this->request->getPost(),
			[
				'product_id',
				'quantity'
			]
		);
		$Transaction->setProviderId( $Provider->getId() );
		
		
		
		
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
		
		$Provider = Provider::findFirst( $id );
		
		if( !$Provider )
		{
			$this->flash->error('This provider does not exist.');
			
		}
		elseif( $Provider->delete() )
		{
			$this->flash->warning('This provider has been removed.');
		}
		else
		{
			$this->flash->error('An error has occured.');
		}
		return (new Response())->redirect('provider');
		
    }
	
	
	/** save Datas from post form form error
     *
     */
	private $formDatas = 'tmp_provider_form';
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