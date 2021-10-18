<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Services\FileProcess\ImportCsv;
use App\Http\Services\TransactionProcessor;

class FullProcessTest extends TestCase
{

    /**
     * Test The Applicatin Full Process.
     *
     * @return void
     */
    public function testFullProcess()
    {
        $fileName = base_path('tests/input/data.csv');
        $allTransactions = (new ImportCsv($fileName))->import();

        $this->assertIsObject($allTransactions);
        $this->assertEquals(13, $allTransactions->count());

        $transactionHistory = [];
        $transactionProcessor = new TransactionProcessor();

        foreach ($allTransactions as $key => $transaction) {
            $response = $transactionProcessor->processTransaction($transaction, $transactionHistory);
            $transactionHistory[] = $transaction;
            $this->assertIsArray($response);
            $this->assertTrue(true, $response['status']);

            switch ($key) {
                case 0:
                    $this->assertEquals("0.60", $response['commission']);
                    break;
                case 1:
                    $this->assertEquals("3.00", $response['commission']);
                    break;
                case 2:
                    $this->assertEquals("0.00", $response['commission']);
                    break;
                case 3:
                    $this->assertEquals("0.06", $response['commission']);
                    break;
                case 4:
                    $this->assertEquals("1.50", $response['commission']);
                    break;
                case 5:
                    $this->assertEquals("0", $response['commission']);
                    break;
                case 6:
                    $this->assertEquals("0.71", $response['commission']);
                    break;
                case 7:
                    $this->assertEquals("0.30", $response['commission']);
                    break;
                case 8:
                    $this->assertEquals("0.30", $response['commission']);
                    break;
                case 9:
                    $this->assertEquals("3.00", $response['commission']);
                    break;
                case 10:
                    $this->assertEquals("0.00", $response['commission']);
                    break;
                case 11:
                    $this->assertEquals("0.00", $response['commission']);
                    break;
                case 12:
                    $this->assertEquals("8624", $response['commission']);
                    break;
                default:
                    break;
            }
        }
    }
}
