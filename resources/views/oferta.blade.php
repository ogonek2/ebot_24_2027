@extends('layouts.app')

@section('title')
Публічна оферта - Єнот 24 Прання/Хімчистка Килимів та Одягу в Києві
@endsection

@php
    $breadcrumbs = [breadcrumb_home(), ['name' => 'Публічна оферта']];
@endphp

@section('content')
    @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <iframe
        src="https://docs.google.com/document/d/1PXa7_SgRVjitoEGtNi0vIa9fQ7Q_Jy0S/edit?usp=sharing&ouid=116808565778515480883&rtpof=true&sd=true"
        width="100%" height="1000vh">
    </iframe>
@endsection
