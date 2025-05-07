<?php

namespace Tests\Unit;

use App\Models\Operation;
use App\Services\CommissionCalculatorService;
use App\Services\ExchangeRateService;
use Tests\TestCase;

class CommissionCalculatorTest extends TestCase
{
    private CommissionCalculatorService $calculator;
    private ExchangeRateService $exchangeRateService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->exchangeRateService = new ExchangeRateService();
        $this->calculator = new CommissionCalculatorService($this->exchangeRateService);
    }
    
    public function testProcessInputFromExample(): void
    {
        // These operations match the example in the task
        $operations = [
            new Operation(new \DateTime('2014-12-31'), 4, 'private', 'withdraw', 1200.00, 'EUR'),
            new Operation(new \DateTime('2015-01-01'), 4, 'private', 'withdraw', 1000.00, 'EUR'),
            new Operation(new \DateTime('2016-01-05'), 4, 'private', 'withdraw', 1000.00, 'EUR'),
            new Operation(new \DateTime('2016-01-05'), 1, 'private', 'deposit', 200.00, 'EUR'),
            new Operation(new \DateTime('2016-01-06'), 2, 'business', 'withdraw', 300.00, 'EUR'),
            new Operation(new \DateTime('2016-01-06'), 1, 'private', 'withdraw', 30000, 'JPY'),
            new Operation(new \DateTime('2016-01-07'), 1, 'private', 'withdraw', 1000.00, 'EUR'),
            new Operation(new \DateTime('2016-01-07'), 1, 'private', 'withdraw', 100.00, 'USD'),
            new Operation(new \DateTime('2016-01-10'), 1, 'private', 'withdraw', 100.00, 'EUR'),
            new Operation(new \DateTime('2016-01-10'), 2, 'business', 'deposit', 10000.00, 'EUR'),
            new Operation(new \DateTime('2016-01-10'), 3, 'private', 'withdraw', 1000.00, 'EUR'),
            new Operation(new \DateTime('2016-02-15'), 1, 'private', 'withdraw', 300.00, 'EUR'),
            new Operation(new \DateTime('2016-02-19'), 5, 'private', 'withdraw', 3000000, 'JPY'),
        ];

        // Expected results from the example
        $expectedResults = [
            0.60,
            3.00,
            0.00,
            0.06,
            1.50,
            0,
            0.70,
            0.30,
            0.30,
            3.00,
            0.00,
            0.00,
            8612,
        ];

        $results = [];
        foreach ($operations as $index => $operation) {
            $commission = $this->calculator->calculate($operation);
            $results[] = $commission;
            
            $currency = trim($operation->getCurrency());
            
            if (strcasecmp($currency, 'JPY') === 0) {
                $formatted = (string)intval($commission);
            } else {
                $formatted = number_format($commission, 2, '.', '');
            }
            
            $this->assertEquals($expectedResults[$index], (float) $formatted, 
                "Commission calculation failed for operation {$index}");
        }
    }
} 