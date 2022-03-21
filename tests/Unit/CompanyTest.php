<?php

class CompanyTest extends \Codeception\Test\Unit
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

    /**
	* Convert external mixed var to real balance
	*
	*/
    public function testBalanceInputConverter()
    {

		$Company = new Company();
		
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
			$output = $Company->setBalance($val)->convertBalanceInput()->getBalance();
			$this->assertTrue( $output === $check);
		}
	
    }
    /**
	* Convert internal int to real display balance
	*
	*/
    public function testBalanceOutputConverter()
    {

		$Company = new Company();
	
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
			$this->assertTrue($Company->setBalance($val)->convertBalanceOutput() === $check);
		}
	
    }
}