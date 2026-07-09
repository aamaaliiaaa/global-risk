<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

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
        return view('country.show', compact('country'));
    }

}