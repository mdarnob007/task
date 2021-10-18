<?php

namespace App\Http\Services;

use App\Http\Services\CommissionCalculation;

class TransactionProcessor implements TransactionInterface
{
    public function processTransaction(object $transaction, array $transactionHistory):array
    {
        $commissionCalculation = new CommissionCalculation($transaction, $transactionHistory);
        if ($transaction->operationType === 'withdraw') {
            if ($transaction->userType === 'private') {
                return $commissionCalculation->getPrivateWithdrawCommission($transaction, $transactionHistory);
            } else {
                return $commissionCalculation->getBusWithdrawCommission($transaction, $transactionHistory);
            }
        } else {
            return $commissionCalculation->getDepositCommission($transaction, $transactionHistory);
        }
    }
}
