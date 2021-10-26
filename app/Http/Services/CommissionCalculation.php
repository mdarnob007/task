<?php

namespace App\Http\Services;

use App\Http\Services\ExchangeCurrency;

class CommissionCalculation implements CommissionInterface
{
    private $depositCommissionRate;
    private $withdrawalPrivateFeeRate;
    private $withdrawalBusinessFeeRate;
    private $privateWithdrawFreeWeeklyAmount;
    private $privateWithdrawFreeWeeklyTranLimit;
    private $baseCurrencyCode;
    private $scale;
    private $transaction;
    private $transactionHistory;
    private $exchangeCurrency;
    private $exchangeRate;

    public function __construct(object $transaction, array $transactionHistory)
    {
        $this->transaction = $transaction;
        $this->transactionHistory = $transactionHistory;
        $this->exchangeCurrency = new ExchangeCurrency();
        $this->setScale();
        $this->baseCurrencyCode = config('commissionSetup.currency_default_code');
        $this->depositCommissionRate = config('commissionSetup.deposit_commission_rate');
        $this->privateWithdrawFreeWeeklyAmount = config('commissionSetup.private_withdrawal_free_weekly_amount');
        $this->privateWithdrawFreeWeeklyTranLimit = config('commissionSetup.private_withdrawal_max_weekly_discounts_number');
        $this->withdrawalPrivateFeeRate = config('commissionSetup.withdrawal_private_fee_rate');
        $this->withdrawalBusinessFeeRate = config('commissionSetup.withdrawal_business_fee_rate');
    }

    public function getDepositCommission(): array
    {
        if ($this->transaction->transactionAmount < 0) {
            return $this->makeResponse(true, $this->transaction->transactionAmount);
        }


        $commission = $this->transaction->transactionAmount * $this->depositCommissionRate / 100;
        return $this->makeResponse(true, $commission);
    }

    public function getBusinessWithdrawCommission(): array
    {
        if ($this->transaction->transactionAmount < 0) {
            return $this->makeResponse(true, $this->transaction->transactionAmount);
        }

        $commission = $this->transaction->transactionAmount * $this->withdrawalBusinessFeeRate / 100;
        return $this->makeResponse(true, $commission);
    }

    public function getPrivateWithdrawCommission(object $transaction, array $transactionHistory): array
    {
        if ($this->transaction->transactionAmount < 0) {
            return $this->makeResponse(true, $this->transaction->transactionAmount);
        }

        if ($this->transaction->currencyCode != $this->baseCurrencyCode) {
            $response = $this->exchangeCurrency->exchange($this->transaction->currencyCode, $this->transaction->transactionDate->format('Y-m-d'));
            if (!$response['status']) {
                return $response;
            }
            $this->exchangeRate = $response['exchange_rate'];
            $this->transaction->eurAmount = $this->transaction->transactionAmount / $this->exchangeRate;
        }

        $amount = $this->getCommissionApplicableWithdrawAmount($transaction, $transactionHistory);

        //apply commission on this transaction
        $commission = $amount * $this->withdrawalPrivateFeeRate / 100;

        return $this->makeResponse(true, $commission);
    }

    private function getCommissionApplicableWithdrawAmount()
    {
        $numberOfTransactionOnAWeek = 1;
        $totalTransactionAmount = ($this->baseCurrencyCode == $this->transaction->currencyCode) ? $this->transaction->transactionAmount : $this->transaction->eurAmount;
        foreach ($this->transactionHistory as $history) {
            if ($history->userId === $this->transaction->userId && $history->userType === 'private' && $history->operationType === 'withdraw' && $this->transaction->transactionDate->format('oW') === $history->transactionDate->format('oW')) {
                $numberOfTransactionOnAWeek++;
                if ($history->currencyCode != $this->baseCurrencyCode) {
                    $totalTransactionAmount += $history->eurAmount;
                } else {
                    $totalTransactionAmount += $history->transactionAmount;
                }
            }
        }
        if ($this->privateWithdrawFreeWeeklyTranLimit < $numberOfTransactionOnAWeek) {
            return $this->transaction->transactionAmount;
        } else {
            return $this->getCommissionOverflowAmount($totalTransactionAmount);
        }
    }

    private function getCommissionOverflowAmount(string $totalTransactionAmount)
    {
        if ($this->privateWithdrawFreeWeeklyAmount > $totalTransactionAmount) {
            return 0;
        } else {
            $amount = $totalTransactionAmount - $this->privateWithdrawFreeWeeklyAmount;

            if ($this->transaction->currencyCode != $this->baseCurrencyCode) {
                $amount = $amount * $this->exchangeRate;
            }
            if ($amount >= $this->transaction->transactionAmount) {
                return $this->transaction->transactionAmount;
            } else {
                return $amount;
            }
        }
    }

    private function makeResponse($status, $commission)
    {
        return ['status' => $status, 'commission' => $this->roundUp($commission)];
    }

    private function roundUp(string $value)
    {
        $pow = pow(10, $this->scale);
        $round = (ceil($pow * $value) + ceil($pow * $value - ceil($pow * $value))) / $pow;
        return number_format($round, $this->scale, '.', '');
    }

    private function setScale()
    {
        $scaleMap = config('commissionSetup.currency_scale_map');
        $this->scale = config('commissionSetup.currency_default_scale');
        foreach ($scaleMap as $key => $value) {
            if ($key == $this->transaction->currencyCode) {
                $this->scale = $value;
            }
        }
    }
}
