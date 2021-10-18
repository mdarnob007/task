<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Services\FileProcess\ImportCsv;
use App\Http\Services\TransactionProcessor;
use App\Http\Validations\InputValidation;

class CalculateCommission extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:commission {fileName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command automatically calculated the commission '
            . 'of transaction CSV that is stored inside public/input/data.csv';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $fileName = $this->argument('fileName');
            $allTransactions = (new ImportCsv(public_path('input/' . $fileName)))->import();

            $validationResponse = (new InputValidation())->csvDataValidator($allTransactions);
            if (!$validationResponse['status']) {
                foreach ($validationResponse['message'] as $error) {
                    $this->error($error);
                }
                return 0;
            }
            $transactionHistory = [];
            $transactionProcessor = new TransactionProcessor();
            if ($allTransactions->count() > 0) {
                foreach ($allTransactions as $transaction) {
                    $response = $transactionProcessor->processTransaction($transaction, $transactionHistory);
                    if ($response['status']) {
                        $this->info($response['commission']);
                    } else {
                        $this->error($response['message']);
                        return 0;
                    }

                    $transactionHistory[] = $transaction;
                }
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
        }

        return 0;
    }
}
