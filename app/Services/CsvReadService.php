<?php

namespace App\Services;

use App\Models\Operation;

class CsvReadService
{
    /**
     * Read operations from a CSV file
     *
     * @param string $filePath
     * @return Operation[]
     */
    public function read(string $filePath): array
    {
        $operations = [];
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Cannot open file: {$filePath}");
        }

        while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (count($data) !== 6) {
                continue; // Skip invalid rows
            }

            [
                $date, 
                $userId, 
                $userType, 
                $operationType, 
                $amount, 
                $currency
            ] = $data;

            $operation = new Operation(
                new \DateTime($date),
                (int) $userId,
                $userType,
                $operationType,
                (float) $amount,
                $currency
            );

            $operations[] = $operation;
        }

        fclose($handle);
        return $operations;
    }
} 