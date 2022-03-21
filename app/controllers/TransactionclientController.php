<?php
/**
* TransactionController management
*
* @author M.Marceau <m.marceau@gmail.com>
*/

use Phalcon\Mvc\Controller;

use Phalcon\Http\Response;


/**
 * @RoutePrefix('/transactionClient')
 */
class TransactionclientController extends Controller
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
		
		
		//associate transactions Client to view
		$this->view->Transactions = Transactionclient::find();
		
		
		
	}
	
	/**
     * @Get(
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
			
			$Employee = new Employee();
			$Employee->assign(
				$this->request->getPost(),
				[
					'company_id',
					'client_id',
					'product_id',
					'quantity',
				]
			);
			
			//patch date format
			$BirthDay = $Employee->getBirthday();
			if(!$BirthDay) $Employee->setBirthday(null);
			
			$Firstday = $Employee->getFirstDayCompany();
			if(!$Firstday) $Employee->setFirstDayCompany(null);
			
			$success = $Employee->save();
			
			
			if(!$success)
			{
				$this->saveFormDatas();
				$messages = $Employee->getMessages();
				foreach($messages as $message)
				{
					$this->flash->error($message);
				}
				return (new Response())->redirect('employee/add'.($companyId?'/'.$companyId:''));
			}
			$this->clearFormDatas();
			
			$this->flash->success("Employee has been created");
			
			return (new Response())->redirect('company/edit/'.$Employee->getCompanyId());
			
			
		}
		
		
		//inserting required assets
		
		$this->assets->addCss('css/fontawesome5.min.css');
		
		
		$Transactionclient = new Transactionclient();
		
		$this->view->Transaction = $this->assignFormDatas($Transactionclient) ;
		
		$this->view->companyDropdown = $this->tag->select(array("company_id", Company::find(), "using" => array("id", "name"), "class" => "form-control"));
		$this->view->clientDropdown = $this->tag->select(array("client_id", Client::find(), "using" => array("id", "name"), "class" => "form-control"));
		$this->view->productDropdown = $this->tag->select(array("product_id", Product::find(), "using" => array("id", "name"), "class" => "form-control"));
        
		
	}
	
	
	/** save Datas from post form form error
     *
     */
	private $formDatas = 'tmp_transclient_form';
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
					'company_id',
					'client_id',
					'product_id',
					'quantity',
				]
				
			);
			$this->clearFormDatas();
		}
		return $Model;
	}
}