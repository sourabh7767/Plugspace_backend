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
    <div class="col-12">
        <strong class="welcome-txt">Welcome To Dashboard</strong>
    </div>
    <div class="row dash-row" style="margin-top:20px;">


        <div class="col-md-4 col-lg-3  box-dashboard">
            <a href="{{ url('admin/users') }}/1">
                <div class="stati bg-wet_asphalt ">
                    <div class="col-10 user-contant">
                        <p>Total Users</p>
                        <span> {{ $totalUsers }}</span>
                    </div>
                    <div class="col-2 text-right">
                        <i class="far fa-user"></i>
                    </div>
                </div>  
            </a>
        </div>  
        <div class="col-md-4 col-lg-3  box-dashboard">
           <a href="{{ url('admin/users') }}/2">
                <div class="stati bg-wet_asphalt ">
                    <div class="col-10 user-contant">
                        <p>Total Biologically (Male-Female)</p>
                        <span> {{ $totalBiologically }}</span>
                    </div>
                    <div class="col-2 text-right">
                        <i class="far fa-user"></i>
                    </div>
                </div>  
            </a>
        </div>  
        <div class="col-md-4 col-lg-3  box-dashboard">
           <a href="{{ url('admin/users') }}/3">
                <div class="stati bg-wet_asphalt ">
                    <div class="col-10 user-contant">
                        <p>Total Male-Female</p>
                        <span> {{ $totalTrans }}</span>
                    </div>
                    <div class="col-2 text-right">
                        <i class="far fa-user"></i>
                    </div>
                </div>  
            </a>
        </div>  
        <div class="col-md-4 col-lg-3  box-dashboard">
           <a href="{{ url('admin/users') }}/4">
                <div class="stati bg-wet_asphalt ">
                    <div class="col-10 user-contant">
                        <p>Total Other Peoples</p>
                        <span> {{ $totalOther }}</span>
                    </div>
                    <div class="col-2 text-right">
                        <i class="far fa-user"></i>
                    </div>
                </div>  
            </a>
        </div>  
        
       
    <!-- </div> -->
 <!-- <div class="row dash-row" style="margin-top:20px;"> -->
        <div class="col-md-4 col-lg-3  box-dashboard">
           <a href="{{ url('admin/users') }}/5">
                <div class="stati bg-wet_asphalt ">
                    <div class="col-10 user-contant">
                        <p>Today Users</p>
                        <span> {{ $todayUsers }}</span>
                    </div>
                    <div class="col-2 text-right">
                        <i class="far fa-user"></i>
                    </div>
                </div>  
            </a>
        </div>  
        <div class="col-md-4 col-lg-3  box-dashboard">
           <a href="{{ url('admin/users') }}/6">
                <div class="stati bg-wet_asphalt ">
                    <div class="col-10 user-contant">
                        <p>Today Biologically (Male-Female)</p>
                        <span> {{ $todayBiologically }}</span>
                    </div>
                    <div class="col-2 text-right">
                        <i class="far fa-user"></i>
                    </div>
                </div>  
            </a>
        </div>  
        <div class="col-md-4 col-lg-3  box-dashboard">
           <a href="{{ url('admin/users') }}/7">
                <div class="stati bg-wet_asphalt ">
                    <div class="col-10 user-contant">
                        <p>Today Male-Female</p>
                        <span> {{ $todayTrans }}</span>
                    </div>
                    <div class="col-2 text-right">
                        <i class="far fa-user"></i>
                    </div>
                </div>  
            </a>
        </div>  
        <div class="col-md-4 col-lg-3  box-dashboard">
           <a href="{{ url('admin/users') }}/8">
                <div class="stati bg-wet_asphalt ">
                    <div class="col-10 user-contant">
                        <p>Today Other Peoples</p>
                        <span> {{ $todayOther }}</span>
                    </div>
                    <div class="col-2 text-right">
                        <i class="far fa-user"></i>
                    </div>
                </div>  
            </a>
        </div>  
       
    </div>


     
</div>
@endsection
