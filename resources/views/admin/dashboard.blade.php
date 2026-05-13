@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1>Xin chào Admin — {{ auth()->user()->name }}</h1>
@endsection