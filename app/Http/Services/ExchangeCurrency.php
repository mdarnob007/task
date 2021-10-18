<?php

namespace App\Http\Services;

use Log;

class ExchangeCurrency {

    private $from;
    private $date;
    private $url;
    private $accessKey;

    public function __construct() {
        $this->url = config('commissionSetup.currency_api_url');
        $this->accessKey = config('commissionSetup.currency_api_key');
    }

    public function exchange($from, $date) {
        $this->from = $from;
        $this->date = $date;

        $data = $this->prepareData();
        $urlWithData = $this->url . $this->date . "?" . $data;
        \Log::info($urlWithData);
        $response = $this->guzzleGetCall($urlWithData);
        Log::info($response);
        if (!$response['status']) {
            return $response;
        }
        return ['status' => true, 'exchange_rate' => $response['data']['rates'][$this->from]];
    }

    private function guzzleGetCall($urlWithData) {
        try {
            $client = new \GuzzleHttp\Client([]);
            $response = $client->request('GET', $urlWithData, ['verify' => false]);
            if ($response->getStatusCode() == 200) {
                $body = $response->getBody();
                if ($body) {
                    \Log::info($body);
                    return ['status' => true, 'data' => json_decode($body, true)];
                }
            }
        } catch (\GuzzleHttp\Exception\ConnectException $ex) {
            Log::error($ex);
            return ['status' => false, 'message' => "Unable to connect with api.exchangeratesapi.io . Please check your internet connection."];
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            $responseBody = $exception->getResponse()->getBody(true);
            $errors = json_decode($responseBody, true);
            if (isset($errors['error']['message'])) {
                return ['status' => false, 'message' => $errors['error']['message']];
            } else {
                return ['status' => false, 'message' => "Something went wrong on exchnage currency api"];
            }
        } catch (\Exception $ex) {
            Log::error($ex);
            dd($ex->getMessage());
            return ['status' => false, 'message' => "Something went wrong on exchnage currency api"];
        }
    }

    private function prepareData() {
        return 'access_key=' . $this->accessKey .
                '&symbols=' . $this->from;
    }

}
