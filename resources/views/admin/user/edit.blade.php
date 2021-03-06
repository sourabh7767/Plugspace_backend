@extends('layouts.app')

@section('content')
<section class="content-header">
    <h1>
        Customer
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($customer, ['route' => ['customer.update', $customer->id], 'method' => 'patch']) !!}
                
                @include('customer.fields1')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection