<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Operation;
use App\Services\CommissionCalculatorService;
use App\Services\CsvReadService;
use App\Services\ExchangeRateService;

// Check if file argument is provided
if ($argc < 2) {
    echo "Usage: php script.php <csv_file>\n";
    exit(1);
}

$filePath = $argv[1];

// Check if file exists
if (!file_exists($filePath)) {
    echo "Error: File not found: {$filePath}\n";
    exit(1);
}

try {
    // Initialize services
    $exchangeRateService = new ExchangeRateService();
    $calculator = new CommissionCalculatorService($exchangeRateService);
    $reader = new CsvReadService();

    // Read operations from CSV
    $operations = $reader->read($filePath);

    // Calculate and output commissions
    foreach ($operations as $operation) {
        $commission = $calculator->calculate($operation);
        
        // Format based on currency
        if (strcasecmp($operation->getCurrency(), 'JPY') === 0) {
            echo number_format($commission, 0, '.', '') . "\n";
        } else {
            echo number_format($commission, 2, '.', '') . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 