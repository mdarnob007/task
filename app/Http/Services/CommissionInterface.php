<?php

namespace App\Http\Services;

interface CommissionInterface
{
    /**
     * getDepositCommission
     *
     * @return array
     */
    public function getDepositCommission():array;
    
    /**
     * getBusWithdrawCommission
     *
     * @return array
     */
    public function getBusWithdrawCommission():array;
    
    /**
     * getPrivateWithdrawCommission
     *
     * @var object
     * @var array
     *
     * @return array
     */
    public function getPrivateWithdrawCommission(object $transaction, array $transactionHistory):array;
}
