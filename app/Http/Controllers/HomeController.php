<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helper\HomeHelper;

class HomeController extends Controller
{
    public function getAsteroidsData(Request $request)
    {
        // date validation
        $validationResponse = Validator::make($request->all(), [
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);
        if ($validationResponse->fails()) {
            return response()
                ->json([
                    'status' => false,
                    'data' => '',
                    'message' => 'Incorrect start and end dates'
                ]);
        }

        // format dates as required for API 
        $data = array(
            'from' => date("Y-m-d", strtotime($request->from)),
            'to' => date("Y-m-d", strtotime($request->to))
        );
        // get data from Neo Feed API
        $response = HomeHelper::getAsteroidsData($data);

        // return error response to front end
        if (isset($response->error_message)) {
            return response()
                ->json([
                    'status' => false,
                    'data' => '',
                    'message' => $response->error_message
                ]);
        }

        // get NEO stats from response
        $dateWiseNearEarthObjects = $response?->near_earth_objects;

        // create empty arrays to collect the data through iteration
        $asteroidSpeedData = array();
        $asteroidDistanceData = array();
        $asteroidDiameterData = array();
        $chartData = array();

        // iterate through data to collect required stats
        foreach ($dateWiseNearEarthObjects as $date => $dateWiseNeos) {

            foreach ($dateWiseNeos as $neo) {

                // get speeds of asteroids
                $speed = $neo->close_approach_data[0]?->relative_velocity?->kilometers_per_hour;

                // get distances of asteroids
                $distance = $neo->close_approach_data[0]?->miss_distance?->kilometers;

                // get minimum and maximum diameters of asteroids
                $estimated_diameter_min = $neo->estimated_diameter?->kilometers?->estimated_diameter_min;
                $estimated_diameter_max = $neo->estimated_diameter?->kilometers?->estimated_diameter_max;

                // collect data into respective arrays
                $asteroidSpeedData[$neo->id] = $speed;
                $asteroidDistanceData[$neo->id] = $distance;
                $asteroidDiameterData[$neo->id] = [$estimated_diameter_min, $estimated_diameter_max];
            }
            // calculate chart data
            $chartData[$date] = count($dateWiseNeos);
        }

        // create arrays to store and send data to frontend
        $fastestAsteroid = array();
        $closestAsteroid = array();

        // calculate fastest asteroid, get the relative id and its average size
        $fastestAsteroid['speed'] = max($asteroidSpeedData);
        $fastestAsteroid['id'] = array_search($fastestAsteroid['speed'], $asteroidSpeedData);
        $diameterDataOfFastestAsteroid = isset($asteroidDiameterData[$fastestAsteroid['id']]) ? $asteroidDiameterData[$fastestAsteroid['id']] : 0;
        $fastestAsteroid['average_size'] = number_format((($diameterDataOfFastestAsteroid[0] + $diameterDataOfFastestAsteroid[1]) / 2), 10);

        // calculate closest asteroid, get therelative id and its average size
        $closestAsteroid['distance'] = min($asteroidDistanceData);
        $closestAsteroid['id'] = array_search($closestAsteroid['distance'], $asteroidDistanceData);
        $diameterDataOfClosestAsteroid = isset($asteroidDiameterData[$closestAsteroid['id']]) ? $asteroidDiameterData[$closestAsteroid['id']] : 0;
        $closestAsteroid['average_size'] = number_format((($diameterDataOfClosestAsteroid[0] + $diameterDataOfClosestAsteroid[1]) / 2), 10);

        // return status response to front end
        return response()
            ->json([
                'status' => true,
                'data' => [
                    'fastest_asteroid_data' => $fastestAsteroid,
                    'closest_asteroid_data' => $closestAsteroid,
                    'chart_data' => [
                        'x_axis_data' => array_keys($chartData),
                        'y_axis_data' => array_values($chartData)
                    ],
                ],
                'message' => 'Data received successfully'
            ]);
    }
}