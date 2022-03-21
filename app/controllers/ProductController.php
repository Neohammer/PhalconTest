<?php
/**
* Product management
*
* @author M.Marceau <m.marceau@gmail.com>
*/

use Phalcon\Mvc\Controller;

use Phalcon\Http\Response;


/**
 * @RoutePrefix('/product')
 */
class ProductController extends Controller
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
	
		//associate products to view
		$this->view->Products = Product::find();
		
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
		
		$Product = Product::findFirst( $id );
		
		if( !$Product )
		{
			$this->flash->error('This product does not exist.');
			return (new Response())->redirect('product');
			
		}
		
		if ($this->request->isPost()) {
			
			$Product->assign(
				$this->request->getPost(),
				[
					'name',
					'price',
					'tax',
					'stock',
				]
			);
			//special : clean  price
			$Product->convertPriceInput();
			$Product->convertTaxInput();
			
			$success = $Product->save();
			
			
			if(!$success)
			{
				$messages = $Product->getMessages();
				foreach($messages as $message)
				{
					$this->flash->error($message);
				}
				return (new Response())->redirect('product/edit/'.$Product->getId());
			}
			
			$this->flash->success("Product has been updated");
			return (new Response())->redirect('product');
			
		}
		
		
		$this->view->Product = $this->assignFormDatas($Product);
		
		
		
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
			
			$Product = new Product();
			$Product->assign(
				$this->request->getPost(),
				[
					'name',
					'price',
					'tax',
					'stock',
				]
			);
			//special : clean  price
			$Product->convertPriceInput();
			$Product->convertTaxInput();
			
			$success = $Product->save();
			
			
			if(!$success)
			{
				$this->saveFormDatas();
				$messages = $Product->getMessages();
				foreach($messages as $message)
				{
					$this->flash->error($message);
				}
				return (new Response())->redirect('product/add');
			}
			
			$this->clearFormDatas();
			
			$this->flash->success("Product has been created");
			return (new Response())->redirect('product');
			
		}
		
		$Product = new Product();
		
		$this->view->Product = $this->assignFormDatas($Product) ;
		
		
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
		
		$Product = Product::findFirst( $id );
		
		if( !$Product )
		{
			$this->flash->error('This product does not exist.');
			
		}
		elseif( $Product->delete() )
		{
			$this->flash->warning('This product has been removed.');
		}
		else
		{
			$this->flash->error('An error has occured.');
		}
		return (new Response())->redirect('product');
		
    }
	
	
	/** save Datas from post form form error
     *
     */
	private $formDatas = 'tmp_product_form';
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
					'price',
					'tax',
					'stock',
				]
				
			);
			$this->clearFormDatas();
		}
		return $Model;
	}
}