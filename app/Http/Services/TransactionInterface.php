<?php

namespace App\Http\Services;

interface TransactionInterface
{
    /**
     * processTransaction
     *
     * @var object
     * @var array
     *
     * @return array
     */
    public function processTransaction(object $transaction, array $transactionHistory):array;
}
