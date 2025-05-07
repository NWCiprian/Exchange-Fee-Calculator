<?php

namespace App\Services;

use App\Models\Operation;

class CommissionCalculatorService
{
    private ExchangeRateService $exchangeRateService;
    private array $withdrawalHistory = [];

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Calculate commission for an operation
     *
     * @param Operation $operation
     * @return float
     */
    public function calculate(Operation $operation): float
    {
        if ($operation->isDeposit()) {
            return $this->calculateDeposit($operation);
        }

        if ($operation->isWithdraw()) {
            if ($operation->isPrivateUser()) {
                return $this->calculatePrivateWithdraw($operation);
            }

            if ($operation->isBusinessUser()) {
                return $this->calculateBusinessWithdraw($operation);
            }
        }

        return 0.0;
    }

    /**
     * Calculate commission for deposit operation
     *
     * @param Operation $operation
     * @return float
     */
    private function calculateDeposit(Operation $operation): float
    {
        $amount = $operation->getAmount();
        $depositRate = config('variables.commission.deposit_rate');
        $commission = $amount * $depositRate;
        
        return $this->roundUp($commission, $operation->getCurrency());
    }

    /**
     * Calculate commission for private withdraw operation
     *
     * @param Operation $operation
     * @return float
     */
    private function calculatePrivateWithdraw(Operation $operation): float
    {
        $userId = $operation->getUserId();
        $date = $operation->getDate();
        $weekStartDate = clone $date;
        $weekStartDate->modify('monday this week');
        $weekKey = $userId . '-' . $weekStartDate->format('Y-m-d');

        if (!isset($this->withdrawalHistory[$weekKey])) {
            $this->withdrawalHistory[$weekKey] = [
                'operations_count' => 0,
                'total_amount_eur' => 0.0
            ];
        }

        $history = &$this->withdrawalHistory[$weekKey];
        $history['operations_count']++;

        $amount = $operation->getAmount();
        $currency = $operation->getCurrency();
        $baseCurrency = config('variables.currency.base_currency');
        $amountInEur = $currency === $baseCurrency 
            ? $amount 
            : $this->exchangeRateService->convert($amount, $currency, $baseCurrency);

        $freeLimit = config('variables.commission.private_free_limit_amount');
        $freeOperationsCount = config('variables.commission.private_free_operations_count');
        $privateWithdrawRate = config('variables.commission.private_withdraw_rate');
        
        $freeAmountLeft = $freeLimit - $history['total_amount_eur'];
        
        // If already exceeded operation count limit or free amount limit
        if ($history['operations_count'] > $freeOperationsCount || $freeAmountLeft <= 0) {
            $commission = $amount * $privateWithdrawRate;
            return $this->roundUp($commission, $currency);
        }

        $history['total_amount_eur'] += $amountInEur;

        // If exceeding free amount, calculate commission only on exceeded part
        if ($history['total_amount_eur'] > $freeLimit) {
            $exceededAmountEur = $history['total_amount_eur'] - $freeLimit;
            $exceededAmount = $currency === $baseCurrency 
                ? $exceededAmountEur 
                : $this->exchangeRateService->convert($exceededAmountEur, $baseCurrency, $currency);
            
            $commission = $exceededAmount * $privateWithdrawRate;
            return $this->roundUp($commission, $currency);
        }

        return 0.0;
    }

    /**
     * Calculate commission for business withdraw operation
     *
     * @param Operation $operation
     * @return float
     */
    private function calculateBusinessWithdraw(Operation $operation): float
    {
        $amount = $operation->getAmount();
        $businessWithdrawRate = config('variables.commission.business_withdraw_rate');
        $commission = $amount * $businessWithdrawRate;
        
        return $this->roundUp($commission, $operation->getCurrency());
    }

    /**
     * Round up to currency's decimal places
     *
     * @param float $amount
     * @param string $currency
     * @return float
     */
    private function roundUp(float $amount, string $currency): float
    {
        $scale = $this->getCurrencyScale($currency);
        $multiplier = 10 ** $scale;
        
        return ceil($amount * $multiplier) / $multiplier;
    }

    /**
     * Get currency decimal places
     *
     * @param string $currency
     * @return int
     */
    private function getCurrencyScale(string $currency): int
    {
        $scales = config('variables.currency.scales');
        return $scales[$currency] ?? $scales['default'];
    }
} 