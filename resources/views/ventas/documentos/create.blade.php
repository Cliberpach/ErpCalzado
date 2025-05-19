@extends('layout')
@section('content')

@section('ventas-active', 'active')
@section('documento-active', 'active')

<ventas-app
:v_sede = "{{json_encode($sede)}}"
:lst_almacenes="{{ json_encode($almacenes) }}"
:lst_departamentos_base="{{ json_encode($departamentos) }}"
:lst_provincias_base="{{ json_encode($provincias) }}"
:lst_distritos_base="{{ json_encode($distritos) }}"
:registrador = "{{json_encode($registrador)}}"
:imginicial="'{{ asset('img/default.png') }}'">
</ventas-app>

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
