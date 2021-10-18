<?php

namespace App\Http\Validations;

use DateTime;
use Illuminate\Support\Facades\Validator;

/**
 * Description of InputValidation
 *
 * @author arnob
 */
class InputValidation implements InputValidationInterface {

    public function validateDate($date, $format = 'Y-m-d'): bool {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    public function csvDataValidator($allData) :array {

        $errors = null;
        try {
            foreach ($allData as $key => $t) {
                $validator = Validator::make((array) $t, [
                            'transactionDate' => 'date|required',
                            'userId' => "required|integer",
                            'userType' => 'required|in:private,business',
                            'operationType' => 'required|in:withdraw,deposit',
                            'transactionAmount' => 'required|numeric',
                            'currencyCode' => 'required|in:'. implode(',', config('commissionSetup.supported_currency_codes'))
                ]);

                if ($validator->fails()) {

                    foreach ($validator->errors()->all() as $e) {
                        $errors[] = $e . " On row no " . ($key + 1);
                    }
                }
            }

            return is_null($errors) ? ['status' => true, 'message' => ''] : ['status' => false, 'message' => $errors];
        } catch (\Exception $ex) {
            \Log::error($ex);
            return ['status' => false, 'message' => ['Unable to validate the given input. Internal server error']];
        }
    }

}
