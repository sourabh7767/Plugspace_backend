    @extends('admin.layouts.app')

@section('content')
<style>
    .t-content{
        padding: 20px;
    }
    .text-uppercase{
        font-size :24px;
    }
</style>
<div class="content"><!--container-->
    <div class="row dash-row" style="margin-top:20px;">
        <div class="col-sm-3 box-dashboard">
            <a href="{!! route('admin.user') !!}">
                <div class="stati bg-wet_asphalt ">
                    <div>
                        <b></b>
                        <span>Users : {{ $countUsers }}</span>
                    </div>
                </div>  
            </a>
        </div>
    </div>
</div>
@endsection
