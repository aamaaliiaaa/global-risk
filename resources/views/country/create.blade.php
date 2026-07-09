@extends('layouts.app')

@section('title','Add Country')

@section('content')

<h1 class="dashboard-title">Add Country</h1>

<p class="page-subtitle">
    Add a new country into the monitoring system.
</p>

<form action="{{ route('countries.store') }}" method="POST" class="country-form">

    @csrf

    <div class="form-group">
        <label>Flag</label>
        <input type="text" name="flag" placeholder="🇮🇩">
    </div>

    <div class="form-group">
        <label>Country Name</label>
        <input type="text" name="name">
    </div>

    <div class="form-group">
        <label>Risk Level</label>

        <select name="risk">

            <option>High</option>
            <option>Medium</option>
            <option>Low</option>

        </select>

    </div>

    <div class="form-group">
        <label>Weather</label>
        <input type="text" name="weather">
    </div>

    <div class="form-group">
        <label>Currency</label>
        <input type="text" name="currency">
    </div>

    <div class="form-group">
        <label>Latitude</label>
        <input type="text" name="latitude">
    </div>

    <div class="form-group">
        <label>Longitude</label>
        <input type="text" name="longitude">
    </div>

    <button class="btn-add">
        Save Country
    </button>

</form>

@endsection