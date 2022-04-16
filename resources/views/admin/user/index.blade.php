@extends('admin.layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><b>Users List</b></h1>
            </div>
        </div>
    </div>
</section>
<div class="content">
    <div class="clearfix"></div>
    <p class="flash">
    @include('flash::message')
    <div id="result"></div>
    </p>
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            @include('admin.user.table')
        </div>
    </div>
    <div class="text-center">

    </div>
</div>
@endsection



