@extends('layouts.app')

@section('title')
Умови використання - Єнот 24 Прання/Хімчистка Килимів та Одягу в Києві
@endsection

@php
    $breadcrumbs = [breadcrumb_home(), ['name' => 'Умови використання']];
@endphp

@section('content')
    @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <iframe
        src="https://docs.google.com/document/d/14MAuekt3zwNflgD7yWtg7kQ5wpggeBFuYfKxrzdWojI/edit?usp=sharing"
        width="100%" height="1000vh">
    </iframe>
@endsection
