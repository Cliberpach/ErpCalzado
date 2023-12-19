@extends('layout') 
@section('content')
@section('kardex-active', 'active')
@section('producto_kardex-active', 'active')
@section('producto_kardex-kardex-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-8">
        <h2 style="text-transform:uppercase"><b>Kardex Producto</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Productos</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight" id="appTables">
    <kardex-products></kardex-products>
</div>

@stop
@section('vue-css')
<style>

</style>
@stop
@section('vue-js')
<script>

</script>
@stop
