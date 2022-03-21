<?php

use Phalcon\Mvc\Model;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Digit 			as ValidationDigit;

class CompanyProducts extends Model
{
    protected $id;
    protected $company_id;
    protected $product_id;
    protected $quantity;
	
	
	public function Initialize()
	{
		//define relationships
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
			'company_id',
			new ValidationDigit([
				'message' => 'Company is invalid',
				'cancelOnFail' => true,
			])
		);
		$Validation->add(
			'product_id',
			new ValidationDigit([
				'message' => 'Product is invalid',
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
	* Get Quantity
	*/
	public function getQuantity()
	{
		return $this->name;
	}
	
	/**
	* Set Quantity
	*
	* @param string $quantity
	*
	* @return this
	*/
	public function setQuantity( $quantity )
	{
		$this->quantity = $quantity;
		return $this;
	}
}