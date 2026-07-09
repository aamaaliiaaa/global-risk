<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::query();

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter Risk
        if ($request->risk) {
            $query->where('risk', $request->risk);
        }

        $countries = $query->get();

        return view('country.index', compact('countries'));
    }
    
    public function show(Country $country)
    {
        $weather = Http::get(
            'https://api.open-meteo.com/v1/forecast',
            [
                'latitude' => $country->latitude,
                'longitude' => $country->longitude,
                "current"=>"temperature_2m,wind_speed_10m,weather_code"
            ]
        )->json();

        $exchangeRate = Http::get(
            'https://open.er-api.com/v6/latest/USD',
        )->json();
        $rate = $exchange['rates'][$country->currency] ?? null;

        $weatherCode = $weather['current']['weather_code'];

        $condition = match ($weatherCode) {

            0 => '☀️ Clear',

            1,2,3 => '🌤️ Partly Cloudy',

            45,48 => '🌫️ Fog',

            51,53,55 => '🌦️ Drizzle',

            61,63,65 => '🌧️ Rain',

            71,73,75 => '❄️ Snow',

            default => '☁️ Cloudy',

        };

        return view(
            'country.show',
            compact('country', 'weather', 'condition', 'rate')
        );
        
    }

    public function create()
    {
        return view('country.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'flag' => 'required',
            'name' => 'required',
            'risk' => 'required',
            'weather' => 'required',
            'currency' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            // Add other fields as necessary
        ]);

        Country::create($request->all());

        return redirect()->route('countries.index')->with('success', 'Country created successfully.');
    }

    public function edit(Country $country)
    {
        return view('country.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {

        $country->update($request->all());

        return redirect()->route('countries.index')->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('countries.index')->with('success', 'Country deleted successfully.');
    }

}