@extends('layouts.admins')
@section('content')

    <div id="app">
        <router-view></router-view>
    </div>
@endsection
  @vite(['resources/css/app.css', 'resources/js/app.js'])