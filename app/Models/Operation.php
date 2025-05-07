<?php

namespace App\Models;

class Operation
{
    public function __construct(
        private \DateTime $date,
        private int $userId,
        private string $userType,
        private string $type,
        private float $amount,
        private string $currency
    ) {
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isDeposit(): bool
    {
        return $this->type === config('variables.operation.TYPE_DEPOSIT');
    }

    public function isWithdraw(): bool
    {
        return $this->type === config('variables.operation.TYPE_WITHDRAW');
    }

    public function isPrivateUser(): bool
    {
        return $this->userType === config('variables.operation.USER_TYPE_PRIVATE');
    }

    public function isBusinessUser(): bool
    {
        return $this->userType === config('variables.operation.USER_TYPE_BUSINESS');
    }
} 