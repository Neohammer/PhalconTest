<?php

use Phalcon\Mvc\Model;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf 	as ValidationPresenceOf;
use Phalcon\Validation\Validator\Digit 			as ValidationDigit;

class Product extends Model
{
    private $id;
    private $name;
    private $price;
    private $tax;
    private $stock;
	
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
			'price',
			new ValidationDigit([
				'message' => 'Price is invalid',
			])
		);
		$Validation->add(
			'stock',
			new ValidationDigit([
				'message' => 'Stock is invalid',
			])
		);
		$Validation->add(
			'tax',
			new ValidationDigit([
				'message' => 'Tax is invalid',
			])
		);
		
		return $this->validate($Validation);
	}
	
	
	
	public function canRemoveFromStock( $quantity )
	{
		return $quantity>=1 && $quantity <= $this->getStock();
	}
	
	
	public function removeFromStock( $quantity )
	{
		if($this->canRemoveFromStock($quantity) )
			$this->setStock( $this->getStock() - $quantity);
		return $this;
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
	* Get Price
	*/
	public function getPrice()
	{
		return $this->price;
	}
	/**
	* Set Price
	*
	* @param int $price
	*
	* @return this
	*/
	public function setPrice( $price )
	{
		$this->price = $price;
		return $this;
	}
	/**
	* convert Price on input
	*
	*
	* @return $this
	*/
	public function convertPriceInput( )
	{
		
		$price = $this->getPrice();
		if($price === null) return $this->setPrice(0);
		
		if($price < 0 ) return $this->setPrice(0);
		
		$price = str_replace(' ','',$price);
		
		if(preg_match('/([\.,]{1})/',$price,$found))
		{
			list($unit,$cents) = explode($found[1],$price);
			if((int)$cents < 10 )
				$cents = str_pad($cents,2,'0',STR_PAD_RIGHT);
			elseif((int)$cents > 100 )
			{
				$nb = strlen($cents);
				$cents = $cents/pow(10,($nb-2));
				$cents = round($cents);
			}
				
				
			$price = $unit.$cents;
			
			return $this->setPrice((int)($price));
		}
		return $this->setPrice((int)($price*100));
	}
	/**
	* convert Price on output
	*
	*
	* @return $price_converted
	*/
	public function convertPriceOutput(  )
	{
		$price = $this->getPrice();
		if( $price === null ) $return = 0;
		elseif( $price < 0 ) $return = 0;
		else
		{
			$return = $price/100;
		}
		
		return number_format($return , 2 , '.','');
	}
	
	
	/**
	* return price with tax
	*
	* @return $price_converted
	*/
	public function getPriceWithTax()
	{
		$tax = $this->getTax();
		if( $tax === null ) $tax = 0;
		$price = $this->getPrice();
		if( $price === null ) $price = 0;
		
		
		return round( ((1+($tax/10000))*$price)/100 , 2);
	}
	
	/**
	* convert tax on input
	*
	* @return $this
	*/
	public function convertTaxInput( )
	{
		
		$tax = $this->getTax();
		if($tax === null) return $this->setTax(0);
		
		if($tax < 0 ) return $this->setTax(0);
		
		$tax = str_replace(' ','',$tax);
		
		if(preg_match('/([\.,]{1})/',$tax,$found))
		{
			list($unit,$cents) = explode($found[1],$tax);
			if((int)$cents < 10 )
				$cents = str_pad($cents,2,'0',STR_PAD_RIGHT);
			elseif((int)$cents > 100 )
			{
				$nb = strlen($cents);
				$cents = $cents/pow(10,($nb-2));
				$cents = round($cents);
			}
				
				
			$tax = $unit.$cents;
			
			return $this->setTax((int)($tax));
		}
		return $this->setTax((int)($tax*100));
	}
	/**
	* convert Tax on output
	*
	* @return $tax_converted
	*/
	public function convertTaxOutput(  )
	{
		$tax = $this->getTax();
		if( $tax === null ) $return = 0;
		elseif( $tax < 0 ) $return = 0;
		else
		{
			$return = $tax/100;
		}
		
		return number_format($return , 2 , '.','');
	}
	
	/**
	* Get Tax
	*/
	public function getTax()
	{
		return $this->tax;
	}
	/**
	* Set Tax
	*
	* @param string $tax
	*
	* @return this
	*/
	public function setTax( $tax )
	{
		$this->tax = $tax;
		return $this;
	}
	
	/**
	* Get Stock
	*/
	public function getStock()
	{
		return $this->stock;
	}
	/**
	* Set Stock
	*
	* @param string $stock
	*
	* @return this
	*/
	public function setStock( $stock )
	{
		$this->stock = $stock;
		return $this;
	}
}