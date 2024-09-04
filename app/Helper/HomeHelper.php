<?php

namespace App\Helper;

class HomeHelper
{
    // store API URL and API KEY in constants
    const API_URL = 'https://api.nasa.gov/neo/rest/v1/feed';

    public static function getAsteroidsData($data)
    {
        // get start and end dates
        $startDate = $data['from'];
        $endDate = $data['to'];

        // initialise cURL session
        $ch = curl_init();

        // set the URL
        curl_setopt($ch, CURLOPT_URL, self::API_URL . '?start_date=' . $startDate . '&end_date=' . $endDate . '&api_key=' . env('API_KEY', 'DEMO_KEY') );

        // set return transfer to true to return the transfer as a string of the return value of curl_exec()
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // set the request type to GET
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        // execute cURL session
        $result = curl_exec($ch);

        // catch error
        if (curl_errno($ch)) {
            return curl_error($ch);
        }

        // close cURL session
        curl_close($ch);

        // return decoded response
        return json_decode($result);
    }
}