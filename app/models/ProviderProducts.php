<?php

use Phalcon\Mvc\Model;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Digit 			as ValidationDigit;

class ProviderProducts extends Model
{
    protected $id;
    protected $provider_id;
    protected $product_id;
    protected $quantity;
	
	
	public function Initialize()
	{
		//define relationships
		$this->belongsTo(
			'provider_id',
			'Provider',
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
			'provider_id',
			new ValidationDigit([
				'message' => 'Prvider is invalid',
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