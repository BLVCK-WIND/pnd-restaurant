@extends('layouts.staff')

@section('title', 'Dashboard')

@section('content')
    <h1>Xin chào Staff — {{ auth()->user()->name }}</h1>
@endsection