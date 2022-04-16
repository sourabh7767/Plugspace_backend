<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/admin/home') }}" class="brand-link">
        {{-- <img src="{{ env('PUBLIC_PATH')}}images/adminlogo.svg"
             alt="{{ config('app.name') }} Logo"
             class="brand-image img-circle elevation-3"> --}}
        <span class="brand-text font-weight-light"><center><strong>{{ config('app.name') }}</strong></center></span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar sidebar-menu  flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @include('admin.layouts.menu')
            </ul>
        </nav>
    </div>
</aside>
