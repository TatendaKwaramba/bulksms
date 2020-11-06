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

                $username = 'sandbox'; // use 'sandbox' for development in the test environment
                $apiKey   = '20846462cadda607f9cae98fee200f3543f1b2022d8c3e6acbaa4fd8fea875fb'; // use your sandbox app API key for development in the test environment
                $AT       = new AfricasTalking($username, $apiKey);

                // Get one of the services
                $sms      = $AT->sms();

                // Use the service
                $result   = $sms->send([
                    'to'      => "".$mobile."",
                    'message' => "Hello ".$mobile." you have received ".$amount." to your account"
                ]);

                array_push($data_array, $result);

            }
                    }

            return $data_array;
    }
}
