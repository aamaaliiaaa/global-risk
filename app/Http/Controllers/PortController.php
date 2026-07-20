<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Port;

class PortController extends Controller
{
    public function index()
    {
        $ports = Port::with('country')->get();

        return view('ports.index', compact('ports'));
    }

    public function show(Port $port)
    {
    // Weather
        $weather = Http::get(
            'https://api.open-meteo.com/v1/forecast',
            [
                'latitude'=>$port->latitude,
                'longitude'=>$port->longitude,
                'current'=>'temperature_2m,wind_speed_10m,weather_code'
            ]
        )->json();

        $condition = 'Unknown';

        if(isset($weather['current']['weather_code'])){

            $code = $weather['current']['weather_code'];

            $condition = match($code){

                0=>'☀️ Clear',

                1,2,3=>'🌤 Partly Cloudy',

                45,48=>'🌫 Fog',

                51,53,55=>'🌦 Drizzle',

                61,63,65=>'🌧 Rain',

                71,73,75=>'❄ Snow',

                default=>'☁ Cloudy'

            };

        }

        return view(
            'ports.show',
            compact(
                'port',
                'weather',
                'condition'
            )
        );
    }
}