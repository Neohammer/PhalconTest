<?php
/**
* Company management
*
* @author M.Marceau <m.marceau@gmail.com>
*/

use Phalcon\Mvc\Controller;

use Phalcon\Http\Response;




/**
 * @RoutePrefix('/company')
 */
class CompanyController extends Controller
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
	
		//associate companies to view
		$this->view->Companies = Company::find();
		
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
		
		$Company = Company::findFirst( $id );
		
		if( !$Company )
		{
			$this->flash->error('This company does not exist.');
			return (new Response())->redirect('company');
			
		}
		
		if ($this->request->isPost()) {
			
			
			
			$Company->assign(
				$this->request->getPost(),
				[
					'name',
					'balance',
					'country',
				]
			);
			//special : clean balance
			$Company->convertBalanceInput();
			
			$success = $Company->save();
			
			
			if(!$success)
			{
				$this->saveFormDatas();
				$messages = $Company->getMessages();
				foreach($messages as $message)
				{
					$this->flash->error($message);
				}
				return (new Response())->redirect('company/edit/'.$Company->getId());
			}
			$this->clearFormDatas();
			
			$this->flash->success("Company has been updated");
			return (new Response())->redirect('company');
			
		}
		
		//inserting required assets
		$this->assets->addCss('css/fontawesome5.min.css');
		$this->assets->addJs('js/jquery.min.js');
		$this->assets->addJs('js/common_list.js');
		
		$this->view->Company = $this->assignFormDatas($Company) ; 
		
		
		//Employees loading
		
		$Employees = Employee::find("company_id = ".$Company->getId());
		
		$this->view->Employees = $Employees;
		
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
			
			$Company = new Company();
			$Company->assign(
				$this->request->getPost(),
				[
					'name',
					'balance',
					'country',
				]
			);
			//special : clean balance
			$Company->convertBalanceInput();
			
			$success = $Company->save();
			
			
			if(!$success)
			{
				$this->saveFormDatas();
				$messages = $Company->getMessages();
				foreach($messages as $message)
				{
					$this->flash->error($message);
				}
				return (new Response())->redirect('company/add');
			}
			$this->clearFormDatas();
			
			$this->flash->success("Company has been created");
			return (new Response())->redirect('company');
			
		}
		
		$Company = new Company();
		
		$this->view->Company = $this->assignFormDatas($Company) ;
		
		
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
		
		$Company = Company::findFirst( $id );
		
		if( !$Company )
		{
			$this->flash->error('This company does not exist.');
			
		}
		elseif( $Company->delete() )
		{
			$this->flash->warning('This company has been removed.');
		}
		else
		{
			$this->flash->error('An error has occured.');
		}
		return (new Response())->redirect('company');
		
    }
	
	/** save Datas from post form form error
     *
     */
	private $formDatas = 'tmp_company_form';
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
					'balance',
					'country'
				]
				
			);
			$this->clearFormDatas();
		}
		return $Model;
	}
}