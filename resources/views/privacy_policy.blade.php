@extends('layouts.app')

@section('title')
Політика конфеденційності - Єнот 24 Прання/Хімчистка Килимів та Одягу в Києві
@endsection

@php
    $breadcrumbs = [breadcrumb_home(), ['name' => 'Політика конфіденційності']];
@endphp

@section('content')
    @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <iframe
        src="https://docs.google.com/document/d/1mx5IMvA-TNEBwmj_PwF-WL_ZSyfqC2nu/edit?usp=sharing&ouid=116808565778515480883&rtpof=true&sd=true"
        width="100%" height="1000vh">
    </iframe>
@endsection
