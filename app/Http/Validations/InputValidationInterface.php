<?php

namespace App\Http\Validations;

interface InputValidationInterface
{
    /**
     * validateDate
     *
     * @var string
     * @var string
     *
     * @return bool
     */
    public function validateDate(string $date, string $format = 'Y-m-d'): bool;

    /**
     * csvDataValidator
     *
     * @var object
     *
     * @return array
     */
    public function csvDataValidator(object $allData):array;
}
