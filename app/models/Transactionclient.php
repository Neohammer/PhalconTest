<?php

use Phalcon\Mvc\Model;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Date 			as ValidationDate;
use Phalcon\Validation\Validator\Digit 			as ValidationDigit;
use Phalcon\Validation\Validator\PresenceOf 	as ValidationPresenceOf;
use Phalcon\Validation\Validator\StringLength 	as ValidationStringLength;

class Transactionclient extends Model
{
    protected $id;
    protected $client_id;
    protected $company_id;
    protected $product_id;
    protected $quantity;
    protected $date;
	
	
	public function Initialize()
	{
		//change table nam
		$this->setSource("transaction_client");
		
		
		//define relationships
		$this->belongsTo(
			'client_id',
			'Client',
			'id'
		);
		$this->belongsTo(
			'company_id',
			'Company',
			'id'
		);
		$this->belongsTo(
			'product_id',
			'Product',
			'id'
		);
	}
	
	
	/**
	* Field Validators
	*
	*/
	public function Validation()
	{
		$Validation = new Validation();
		
		$Validation->add(
			'client_id',
			//alnum validation does not work with accentued caracter...
			new ValidationPresenceOf([
				'message' => 'Client is required',
				'cancelOnFail' => true,
			])
		);
		$Validation->add(
			'company_id',
			//alnum validation does not work with accentued caracter...
			new ValidationPresenceOf([
				'message' => 'Company is required',
				'cancelOnFail' => true,
			])
		);
		$Validation->add(
			'product_id',
			//alnum validation does not work with accentued caracter...
			new ValidationPresenceOf([
				'message' => 'Product is required',
				'cancelOnFail' => true,
			])
		);
		$Validation->add(
			'client_id',
			//alnum validation does not work with accentued caracter...
			new ValidationPresenceOf([
				'message' => 'Client is required',
				'cancelOnFail' => true,
			])
		);
		$Validation->add(
			'quantity',
			new ValidationDigit([
				'message' => 'Quantity is invalid',
				'cancelOnFail' => true,
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
	* Get CompanyId
	*/
	public function getCompanyId()
	{
		return $this->company_id;
	}
	
	
	/**
	* Set CompanyId
	*
	* @param string $id
	*
	* @return $this
	*/
	public function setCompanyId( $id )
	{
		$this->company_id = $id;
		return $this;
	}
	/**
	* Get CompanyId
	*/
	public function getClientId()
	{
		return $this->client_id;
	}
	
	
	/**
	* Set CompanyId
	*
	* @param string $id
	*
	* @return $this
	*/
	public function setClientId( $id )
	{
		$this->client_id = $id;
		return $this;
	}
	
	/**
	* Get Quantity
	*/
	public function getQuantity()
	{
		return $this->quantity;
	}
	
	
	/**
	* Set Quantity
	*
	* @param string $name
	*
	* @return this
	*/
	public function setQuantity( $quantity )
	{
		$this->quantity = $quantity;
		return $this;
	}
	
	/**
	* Get Quantity
	*/
	public function getDate()
	{
		return $this->date;
	}
	
}