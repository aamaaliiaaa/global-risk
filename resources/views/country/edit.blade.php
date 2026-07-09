@extends('layouts.app')

@section('title','Edit Country')

@section('content')

<h1 class="dashboard-title">Edit Country</h1>

<p class="page-subtitle">
    Edit the details of an existing country.
</p>

<form action="{{ route('countries.update', $country->id) }}" method="POST">

    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Flag</label>
        <input type="text" name="flag" value="{{ $country->flag }}">
    </div>

    <div class="form-group">
        <label>Country Name</label>
        <input type="text" name="name" value="{{ $country->name }}">
    </div>

    <div class="form-group">
        <label>Risk Level</label>

        <select name="risk">

            <option {{ $country->risk == 'High' ? 'selected' : '' }}>High</option>
            <option {{ $country->risk == 'Medium' ? 'selected' : '' }}>Medium</option>
            <option {{ $country->risk == 'Low' ? 'selected' : '' }}>Low</option>

        </select>

    </div>

    <div class="form-group">
        <label>Weather</label>
        <input type="text" name="weather" value="{{ $country->weather }}">
    </div>

    <div class="form-group">
        <label>Currency</label>
        <input type="text" name="currency" value="{{ $country->currency }}">
    </div>

    <div class="form-group">
        <label>Latitude</label>
        <input type="text" name="latitude" value="{{ $country->latitude }}">
    </div>

    <div class="form-group">
        <label>Longitude</label>
        <input type="text" name="longitude" value="{{ $country->longitude }}">
    </div>

    <button class="btn-add">
        Update Country
    </button>

</form>

@endsection