<?php
/**
* Employee management
*
* @author M.Marceau <m.marceau@gmail.com>
*/

use Phalcon\Mvc\Controller;

use Phalcon\Http\Response;




/**
 * @RoutePrefix('/employee')
 */
class EmployeeController extends Controller
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
		$this->view->Employees = Employee::find();
		
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
		
		$Employee = Employee::findFirst( $id );
		
		if( !$Employee )
		{
			$this->flash->error('This employee does not exist.');
			return (new Response())->redirect('employee');
			
		}
		
		if ($this->request->isPost()) {
			
			
			$Employee->assign(
				$this->request->getPost(),
				[
					'name',
					'birthday',
					'country',
					'first_day_company',
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
				return (new Response())->redirect('employee/edit/'.$Employee->getId());
			}
			$this->clearFormDatas();
			
			$this->flash->success("Employee has been updated");
			return (new Response())->redirect('company/edit/'.$Employee->getCompanyId());
			
		}
		
		//inserting required assets
		$this->assets->addCss('css/fontawesome5.min.css');
		$this->assets->addJs('js/jquery.min.js');
		$this->assets->addJs('js/common_list.js');
		
		$this->view->Employee = $this->assignFormDatas($Employee) ; 
		
		
		$this->view->Company = (new Company())->findFirst($this->view->Employee->getCompanyId());
    }
	/**
     * @Get(
     *     '/add'
     * )
     * @Get(
     *     '/add/{id:[0-9]+}'
     * )
     * @Post(
     *     '/add'
     * )
     */
	public function addAction( $companyId = null )
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
					'name',
					'company_id',
					'birthday',
					'country',
					'first_day_company',
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
		
		$Employee = new Employee();
		
		$this->view->Employee = $this->assignFormDatas($Employee) ;
		$this->view->Company = null;
		
		if( $companyId )
		{
			
			$this->view->Company = (new Company())->findFirst($companyId);
		}
		
		
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
		
		$Employee = Employee::findFirst( $id );
		$Company = null;
		
		if( !$Employee )
		{
			$this->flash->error('This employee does not exist.');
			
		}
		else
		{
			$Company = $Employee->getCompanyId();
			
			if( $Employee->delete() )
			{
				$this->flash->warning('This employee has been removed.');
			}
			else
			{
				$this->flash->error('An error has occured.');
			}
			
		}
		if( $Company )
		{
			return (new Response())->redirect('company/edit/'.$Company);
		}
		return (new Response())->redirect('employee');
		
    }
	
	/** save Datas from post form form error
     *
     */
	private $formDatas = 'tmp_employee_form';
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
					'birthday',
					'company_id',
					'country',
					'first_day_company',
				]
				
			);
			$this->clearFormDatas();
		}
		return $Model;
	}
}