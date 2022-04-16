<div class="col-12 text-center Administrator">
    <img src="{{ env('PUBLIC_PATH')}}images/logo.png" alt="" class="img-responsive">
    <h5>Administrator <i class="fas fa-circle "></i></h5>
</div>

<li class="{{ Request::is('admin/home*') ? 'active' : '' }}">
    <a href="{!! route('admin.home') !!}"><i class="fa fa fa-home"></i><span> Dashboard</span></a>
</li>
<li class="{{ Request::is('admin/user*') ? 'active' : '' }}">
    <a href="{!! route('admin.user') !!}"><i class="fa fa-user-friends"></i><span> Users List</span></a>
</li>
<li class="nav-item">                                                                                                                
    <a href="{{ url('admin/plugspaceUser') }}" class="nav-link {{ Request::is('admin/plugspaceUser*') ? 'active' : '' }}">
        <i class="fas fa-cube" aria-hidden="true"></i>
        <p>&nbsp;PlugSpace Rank</p>
    </a>
</li>
<li class="nav-item">                                                                                                                
    <a href="{{ url('admin/plugspaceText') }}" class="nav-link {{ Request::is('admin/plugspaceText*') ? 'active' : '' }}">
        <i class="fas  fa-globe" aria-hidden="true"></i>
        <p>&nbsp;Rank Text</p>
    </a>
</li>
