<?php

namespace App\Http\Services\FileProcess;

use App\Http\Services\FileProcess;
use App\Http\Validations\InputValidation;
use DateTime;
use InvalidArgumentException;

class ImportCsv extends InputValidation implements FileProcess\FileProcessInterface
{
    private $filePath;

    public function __construct(string $path)
    {
        $this->filePath = $path;
    }

    public function import(): object
    {
        if (!file_exists($this->filePath)) {
            throw new InvalidArgumentException("The provided file " . $this->filePath . " is not a valid file");
        }
        $data = [];
        try {
            $inputs = array_map('str_getcsv', file($this->filePath));
            if (count($inputs) <= 0) {
                return collect($data);
            }
            foreach ($inputs as $key => $value) {
                $data[] = (Object) array(
                            'transactionDate' => isset($value[0]) ? $this->validateDate($value[0]) ? new DateTime($value[0]) : null : null,
                            'userId' => isset($value[1]) ? $value[1] : null,
                            'userType' => isset($value[2]) ? $value[2] : null,
                            'operationType' => isset($value[3]) ? $value[3] : null,
                            'transactionAmount' => isset($value[4]) ? $value[4] : null,
                            'currencyCode' => isset($value[5]) ? $value[5] : null,
                            'id' => $key
                );
            }
            return collect($data, true);
        } catch (\Exception $ex) {
            Log::error($ex);
        }
    }
}
