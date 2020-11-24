<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AfricasTalking\SDK\AfricasTalking;

use Illuminate\Support\Facades\Storage;

class SMS extends Controller
{
    //
    public function upload(Request $request) 
    {
        Storage::delete('beneficiaries.csv');
        $file = $request->file('file')->storeAs(
            'beneficiaries', 'beneficiaries.csv'
        );
        return "Done";
    }

    public function sendSMS(Request $request){

        $data_array = array();
        $path =  \storage_path('app/beneficiaries/beneficiaries.csv');
        $data = array_map("str_getcsv", file($path));
        $csv_data = array_slice($data, 0);

        $flag = true;
        foreach($csv_data as $row) {

            if($flag){
                $flag = false;
            } else {
                $mobile = $row[0];
                $amount = $row[1];

                $username = 'aku-pay'; // use 'sandbox' for development in the test environment
                $apiKey   = 'd3b3c4c4ad03cee17adcc50cecd84d3397cc176e79261971c44da6892c1c12e6'; // use your sandbox app API key for development in the test environment
                $AT       = new AfricasTalking($username, $apiKey);

                // Get one of the services
                $sms      = $AT->sms();

                // Use the service
                $result   = $sms->send([
                    'to'      => "".$mobile."",
                    'message' => "Congratulations! You have been pre-approved for a TraderMoni micro-loan. Funds have been disbursed to your akupay wallet. To access your wallet visit: https://akupay.ng/ OR dial 347*215# OR SMS 'Pay, GTB, 0046732833' to 34461 with the name of your Bank and your account number."
                ]);

                array_push($data_array, $result);

            }
                    }

            return $data_array;
    }
}

