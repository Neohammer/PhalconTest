<?php

class ProductTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

	
	public function testCanRemoveFromStock()
	{
		
		$Product = new Product();
		
		$Tests = [
			//type / stock / rmv  
			[false , 0		, 2				],
			[false , 2		, -2			],
			[true , 4		, 2				],
		];
		
		foreach($Tests as $test)
		{
			list($case , $stock , $rmv ) = $test;
			$output = $Product->SetStock($stock)->canRemoveFromStock($rmv);
			if( $case )
				$this->assertTrue( $output );
			else
				$this->assertFalse( $output );
		}
	}

	
	public function testRemoveFromStock()
	{
		
		$Product = new Product();
		
		$Tests = [
			//type / stock / rmv  / left
			[true , 0		, 2		, 0		],
			[true , 2		, -2	, 2		],
			[true , 4		, 2		, 2		],
		];
		
		foreach($Tests as $test)
		{
			list($case , $stock , $rmv ,$left ) = $test;
			$output = $Product->SetStock($stock)->removeFromStock($rmv);
			$this->assertTrue( $output->getStock() === $left );
		}
	}
    
    /**
	* Convert external mixed var to real balance
	*
	*/
    public function testPriceInputConverter()
    {

		$Product = new Product();
		
		$Tests = [
		
			[true , -2			, 0			],
			[true , null		, 0			],
			[true , "5"			, 500		],
			[true , "5.00"		, 500		],
			
			[true , "0.255"		, 26		],
			[true , "0.25"		, 25		],
			
			[true , "5.50"		, 550		],
			[true , "5,50"		, 550		],
			[true , "5 500"		, 550000	],
			[true , "5 500.55"	, 550055	],
			[true , "5 500,55"	, 550055	],
			[true , "5 500,554"	, 550055	],
			[true , "5 500,555"	, 550056	]
		
		];
		
		foreach($Tests as $test)
		{
			list($case , $val , $check ) = $test;
			$output = $Product->setPrice($val)->convertPriceInput()->getPrice();
			$this->assertTrue( $output === $check);
		}
	
    }
    /**
	* Convert internal int to real display balance
	*
	*/
    public function testPriceOutputConverter()
    {

		$Product = new Product();
	
		$Tests = [
		
			[true , -2		,"0.00"],
			[true , null	,"0.00"],
			[true , 50		, "0.50"],
			[true , 5		,"0.05"],
			[true , 5000	,"50.00"]
		];
		
		foreach($Tests as $test)
		{
			list($case , $val , $check ) = $test;
			$this->assertTrue($Product->setPrice($val)->convertPriceOutput() === $check);
		}
	
    }
    /**
	* Convert external mixed var to real Tax
	*
	*/
    public function testTaxInputConverter()
    {

		$Product = new Product();
		
		$Tests = [
		
			[true , -2			, 0			],
			[true , null		, 0			],
			[true , "5"			, 500		],
			[true , "5.00"		, 500		],
			
			[true , "0.255"		, 26		],
			[true , "0.25"		, 25		],
			
			[true , "5.50"		, 550		],
			[true , "20"		, 2000		],
		
		];
		
		foreach($Tests as $test)
		{
			list($case , $val , $check ) = $test;
			$output = $Product->setTax($val)->convertTaxInput()->getTax();
			$this->assertTrue( $output === $check);
		}
	
    }
    /**
	* Convert internal int to real display balance
	*
	*/
    public function testTaxOutputConverter()
    {

		$Product = new Product();
	
		$Tests = [
		
			[true , -2		,"0.00"],
			[true , null	,"0.00"],
			[true , 50		, "0.50"],
			[true , 5		,"0.05"],
			[true , 5000	,"50.00"]
		];
		
		foreach($Tests as $test)
		{
			list($case , $val , $check ) = $test;
			$this->assertTrue($Product->setTax($val)->convertTaxOutput() === $check);
		}
	
    }
	
	public function testPriceWithTax()
	{
		
		
		$Product = new Product();
	
		$Tests = [
			//bool , price , taxe , result
			[true , null	,null 	, 0.00],
			[true , 2000	,null 	, 20.00],
			[true , 2000	,2000 	, 24.00],
			[true , 4000	,0		, 40.00],
			[true , 2537	,1875	, 30.13]
		];
		
		foreach($Tests as $test)
		{
			list($case , $price , $tax , $check ) = $test;
			$Product
				->setPrice($price)
				->setTax( $tax );
				
				//die($Product->getPriceWithTax());
			$this->assertTrue($Product->getPriceWithTax() === $check);
		}
		
		
		
	}
}