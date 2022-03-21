<?php
/**
* Administrator model
*
* @author M.Marceau <ma.marceau@gmail.com>
*/
use Phalcon\Mvc\Model;


use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf 	as ValidationPresenceOf;
use Phalcon\Validation\Validator\StringLength 	as ValidationStringLength;

class Client extends Model
{
    protected $id;
    protected $name;
    protected $address;
    protected $country;
	
	
	
	/**
	* Field Validators
	*
	*/
	public function Validation()
	{
		$Validation = new Validation();
		
		$Validation->add(
			'name',
			//alnum validation does not work with accentued caracter...
			new ValidationPresenceOf([
				'message' => 'Your name is invalid',
				'cancelOnFail' => true,
			])
		);
		$Validation->add(
			'address',
			//alnum validation does not work with accentued caracter...
			new ValidationPresenceOf([
				'message' => 'Your address is invalid',
				'allowEmpty' => true,
			])
		);
		$Validation->add(
			'country',
			new ValidationStringLength([ 
				"max"            => 2,
				"min"            => 2,
				"messageMaximum" => "Country code must contains 2 character.",
				"messageMinimum" => "Country code must contains 2 character.",
				'allowEmpty' => true,
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
	* Set ID
	*
	* Only for test purpose
	* @param string $name
	*
	* @return this
	*/
	public function setId( $id )
	{
		$this->id = $id;
		return $this;
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
	* Get Address
	*/
	public function getAddress()
	{
		return $this->address;
	}
	/**
	* Set Address
	*
	* @param string $address
	*
	* @return this
	*/
	public function setAddress( $address )
	{
		$this->address = $address;
		return $this;
	}
	
	/**
	* Get Country
	*/
	public function getCountry()
	{
		return $this->country;
	}
	/**
	* Set Country
	*
	* @param string $country
	*
	* @return this
	*/
	public function setCountry( $country )
	{
		$this->country = $country;
		return $this;
	}
	
}