<?php

namespace App\Console\Commands;

use App\Services\CommissionCalculatorService;
use App\Services\CsvReadService;
use Illuminate\Console\Command;

class CalculateCommissionCommand extends Command
{
    protected $signature = 'commission:calculate {file}';
    protected $description = 'Calculate commission fees for operations in a CSV file';

    public function handle(CommissionCalculatorService $calculator, CsvReadService $reader)
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error("CSV not found: {$filePath}");
            return 1;
        }

        $operations = $reader->read($filePath);
        
        // I did notice the zero decimal usage in business context for the yen inside the task :)
        // https://www.chargebee.com/docs/2.0/site-configuration/articles-and-faq/what-are-zero-decimal-currencies.html
        foreach ($operations as $operation) {
            $commission = $calculator->calculate($operation);
            //Make sure no spaces no matter our API provider
            $currency = trim($operation->getCurrency());
            
            if (strcasecmp($currency, 'JPY') === 0) {
                // For JPY, display as integer with no decimal places
                $this->line((string)intval($commission));
            } else {
                $this->line(number_format($commission, 2, '.', ''));
            }
        }
        
        return 0;
    }
} 