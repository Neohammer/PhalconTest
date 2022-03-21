<?php
/**
* Administrator model
*
* @author M.Marceau <ma.marceau@gmail.com>
*/
use Phalcon\Mvc\Model;


use Phalcon\Validation;
use Phalcon\Validation\Validator\Email 			as ValidationEmail;
use Phalcon\Validation\Validator\Alnum 			as ValidationAlnum;
use Phalcon\Validation\Validator\Regex 			as ValidationRegexp;
use Phalcon\Validation\Validator\PresenceOf 	as ValidationPresenceOf;
use Phalcon\Validation\Validator\StringLength 	as ValidationStringLength;

class Administrator extends Model
{
    protected $id;
    protected $name;
    protected $login;
    protected $password;
		
	
	public function Validation()
	{
		$Validation = new Validation();
		
		
		$Validation->add(
			'email',
			new ValidationEmail([
				'message' => 'Your login is invalid',
				'cancelOnFail' => true,
			])
		);
		
		$Validation->add(
			'password',
			new ValidationPresenceOf([
				'message' => 'Password is required',
				'cancelOnFail' => true,
			])
		);
		
		$Validation->add(
			'password',
			new ValidationStringLength([
				'messageMinimum' => 'The password is too short',
				'min'            => 4,
				'messageMaximum' => 'The password is too long',
				'max'            => 20,
			])
		);
		
		$Validation->add(
			'password',
			new  ValidationAlnum([
				'message' => 'Only alphanumerical characters are allowed.',
				'cancelOnFail' => true,
			])
		);
		
		
		$Validation->add(
			'password',
			new ValidationRegexp([
				'message' => 'Password require at least 1 lowercase letter.',
				'pattern' => '/[a-z]+/',
			])
		);
		$Validation->add(
			'password',
			new ValidationRegexp([
				'message' => 'Password require at least 1 capital letter.',
				'pattern' => '/[A-Z]+/',
			])
		);
		$Validation->add(
			'password',
			new ValidationRegexp([
				'message' => 'Password require at least 1 number.',
				'pattern' => '/[0-9]+/',
			])
		);
		
		return $this->validate($Validation);
	}
	
	/** SETTERS AND GETTERS **/
	
	public function getId()
	{
		return $this->id;
	}
	
	/**
	* Get Name
	*/
	public function getName()
	{
		return $this->name;
	}
	
	/**
	* Set Name
	*
	* @param string $name
	*
	* @return this
	*/
	public function setName( $name )
	{
		$this->name = $name;
		return $this;
	}
	
	/**
	* Get Login
	*/
	public function getLogin()
	{
		return $this->login;
	}
	
	/**
	* Set Login
	*
	* @param string $login
	*
	* @return this
	*/
	public function setLogin( $login )
	{
		$this->login = $login;
		return $this;
	}
	
	
	/**
	* Get Password
	*/
	public function getPassword()
	{
		return $this->password;
	}
	
	/**
	* Set Password
	*
	* @param string $password
	*
	* @return this
	*/
	public function setPassword( $password )
	{
		$this->password = $password;
		return $this;
	}
	
	/**
	* Get ReadOnly mode
	*/
	public function getReadOnly()
	{
		return $this->read_only;
	}
	
	/**
	* Set ReadOnly
	*
	* @param string $read_only
	*
	* @return this
	*/
	public function setReadOnly( $read_only )
	{
		$this->read_only = $read_only;
		return $this;
	}
	
}