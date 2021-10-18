<?php

namespace App\Http\Controllers;

use App\Http\Services\FileProcess\ImportCsv;
use App\Http\Services\TransactionProcessor;
use App\Http\Validations\InputValidation;
use Log;

class HomeController extends Controller {

    public function index() {
        
        try {
            $allTransactions = (new ImportCsv('input/data.csv'))->import();
            /* validated transactions csv data */

            $validationResponse = (new InputValidation())->csvDataValidator($allTransactions);
            if (!$validationResponse['status']) {
                foreach ($validationResponse['message'] as $error) {
                    echo $error . "<br/>";
                }

                return;
            }

            $transactionHistory = [];
            $transactionProcessor = new TransactionProcessor();
            if ($allTransactions->count() > 0) {
                foreach ($allTransactions as $transaction) {
                    $response = $transactionProcessor->processTransaction($transaction, $transactionHistory);

                    echo $response['status'] ? $response['commission'] : $response['message'];
//                echo "  ".$transaction->transactionDate->format('Y-m-d').", ".$transaction->userId.", ".$transaction->userType.", ".$transaction->operationType.", ".$transaction->transactionAmount.", ".$transaction->currencyCode."<br/>";
                    echo "<br/>";
                    $transactionHistory[] = $transaction;
                }
            }
        } catch (\Exception $ex) {
            Log::error($ex);
            echo "Something Went Wrong. " . $ex->getMessage();
        }
    }

}
