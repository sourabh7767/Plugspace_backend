@extends('admin.layouts.app')

@section('content')
<section class="content-header">
    <h1>
        Send Notifications
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::open(['route' => ['sendnotification'], 'method' => 'post']) !!}
                
                <div class="form-group">
                    <textarea class="form-control" cols="30" rows="5" placeholder="Notification Message" name="noti_text"></textarea>
                </div>

                <div class="form-customer col-sm-12">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                    <a href="{!! route('admin.user') !!}" class="btn btn-default">Cancel</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection