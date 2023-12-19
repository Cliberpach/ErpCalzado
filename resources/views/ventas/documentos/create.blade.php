@extends('layout') @section('content')

@section('ventas-active', 'active')
@section('documento-active', 'active')
<ventas-app :imginicial="'{{ asset('img/default.png') }}'"></ventas-app>
@stop
@push('styles')
<style>
    .letrapequeña {
        font-size: 11px;
    }
</style>
<link rel="stylesheet" href="/css/appPages.css">
@endpush

@push('scripts-vue-js')
<script src="{{'/js/appPages.js?v='.rand() }}"></script>
@endpush