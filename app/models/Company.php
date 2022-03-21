<?php

use Phalcon\Mvc\Model;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf 	as ValidationPresenceOf;
use Phalcon\Validation\Validator\StringLength 	as ValidationStringLength;
use Phalcon\Validation\Validator\Digit 			as ValidationDigit;

class Company extends Model
{
    protected $id;
    protected $name;
    protected $balance;
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
				'message' => 'Name is invalid',
				'cancelOnFail' => true,
			])
		);
		$Validation->add(
			'balance',
			//alnum validation does not work with accentued caracter...
			new ValidationDigit([
				'message' => 'Balance is invalid',
				'cancelOnFail' => true,
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
	* Get Balance
	*/
	public function getBalance()
	{
		return $this->balance;
	}
	/**
	* Set Balance
	*
	* @param int $balance
	*
	* @return this
	*/
	public function setBalance( $balance )
	{
		$this->balance = $balance;
		return $this;
	}
	/**
	* convert Balance on input
	*
	* @param mixed $balance
	*
	* @return $balance_converted
	*/
	public function convertBalanceInput( )
	{
		
		$balance = $this->getBalance();
		if($balance === null) return $this->setBalance(0);
		
		if($balance < 0 ) return $this->setBalance(0);
		
		$balance = str_replace(' ','',$balance);
		
		if(preg_match('/([\.,]{1})/',$balance,$found))
		{
			list($unit,$cents) = explode($found[1],$balance);
			if((int)$cents < 10 )
				$cents = str_pad($cents,2,'0',STR_PAD_RIGHT);
			elseif((int)$cents > 100 )
			{
				$nb = strlen($cents);
				$cents = $cents/pow(10,($nb-2));
				$cents = round($cents);
			}
				
				
			$balance = $unit.$cents;
			
			return $this->setBalance((int)($balance));
		}
		return $this->setBalance((int)($balance*100));
	}
	/**
	* convert Balance on output
	*
	* @param mixed $balance
	*
	* @return $balance_converted
	*/
	public function convertBalanceOutput(  )
	{
		$balance = $this->getBalance();
		if( $balance === null ) $return = 0;
		elseif( $balance < 0 ) $return = 0;
		else
		{
			$return = $balance/100;
		}
		
		return number_format($return , 2 , '.','');
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