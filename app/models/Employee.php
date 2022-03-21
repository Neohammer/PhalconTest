<?php

use Phalcon\Mvc\Model;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Date 			as ValidationDate;
use Phalcon\Validation\Validator\Digit 			as ValidationDigit;
use Phalcon\Validation\Validator\PresenceOf 	as ValidationPresenceOf;
use Phalcon\Validation\Validator\StringLength 	as ValidationStringLength;

class Employee extends Model
{
    protected $id;
    protected $name;
    protected $birthday;
    protected $country;
    protected $first_day_company;
	
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
				'message' => 'Name is invalid',
				'cancelOnFail' => true,
			])
		);
		$Validation->add(
			'company_id',
			new ValidationDigit([
				'message' => 'Company is invalid',
				'cancelOnFail' => true,
			])
		);
		$Validation->add(
			'birthday',
			new ValidationDate([
				'message' => 'BirthDay is invalid',
				'allowEmpty' => true,
			])
		);
		$Validation->add(
			'first_day_company',
			new ValidationDate([
				'message' => 'First day on company is invalid',
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
	* Get Birthday
	*/
	public function getBirthday()
	{
		return $this->birthday;
	}
	/**
	* SetBirthday
	*
	* @param string $birthday
	*
	* @return this
	*/
	public function setBirthday( $birthday )
	{
		$this->birthday = $birthday;
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
	
	/**
	* Get Company ID
	*/
	public function getCompanyId()
	{
		return $this->company_id;
	}
	/**
	* Set Company ID
	*
	* @param string $country
	*
	* @return this
	*/
	public function setCompanyId( $company_id )
	{
		$this->company_id = $company_id;
		return $this;
	}
	/**
	* Get First day on compagnie
	*/
	public function getFirstDayCompany()
	{
		return $this->first_day_company;
	}
	/**
	* Set First day on compagnie
	*
	* @param string $country
	*
	* @return this
	*/
	public function setFirstDayCompany( $first_day_company )
	{
		$this->first_day_company = $first_day_company;
		return $this;
	}
}