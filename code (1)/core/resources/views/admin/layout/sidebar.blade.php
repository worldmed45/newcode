<div class="page-sidebar-wrapper">

    <div class="page-sidebar navbar-collapse collapse">

        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">

            <li class="sidebar-toggler-wrapper hide">
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                <div class="sidebar-toggler"> </div>
                <!-- END SIDEBAR TOGGLER BUTTON -->
            </li>

            <li class="nav-item  @if(request()->path() == 'admin/home') active open @endif">
                <a href="{{url('admin/home')}}" class="nav-link nav-toggle">
                    <i class="icon-home"></i>
                    <span class="title">Dashboard</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="nav-item
            @if(request()->path() == 'admin/users') active open
                @elseif(request()->path() == 'admin/user/search') active open
                @elseif(request()->path() == 'admin/user-banned') active open
                @elseif(request()->path() == 'admin/broadcast') active open
                @elseif(request()->path() == 'admin/subscribers') active open
            @endif">
            <a href="#" class="nav-link nav-toggle">
                <i class="fa fa-users"></i>
                <span class="title">User Management</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item @if(request()->path() == 'admin/users') active open 
                    @elseif(request()->path() == 'admin/user/search') active open
                    @endif">
                    <a href="{{route('users')}}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">Users</span>
                    </a>
                </li>
                <li class="nav-item @if(request()->path() == 'admin/broadcast') active open @endif">
                    <a href="{{route('broadcast')}}" class="nav-link ">
                        <i class="icon-envelope"></i>
                        <span class="title">Broadcast Email</span>
                    </a>
                </li>
                <li class="nav-item @if(request()->path() == 'admin/subscribers') active open @endif">
                    <a href="{{route('admin.subscribers')}}" class="nav-link ">
                        <i class="fa fa-users"></i>
                        <span class="title">Subscribers</span>
                    </a>
                </li>    
                <li class="nav-item @if(request()->path() == 'admin/user-banned') active open @endif">
                    <a href="{{route('user.ban')}}" class="nav-link ">
                        <i class="icon-user"></i>
                        <span class="title">Banned Users</span>
                    </a>
                </li>                 
            </ul>
        </li>
            <!--<li class="nav-item  @if(request()->path() == 'admin/gateway') active open @endif">
                <a href="{{route('gateway.index')}}" class="nav-link nav-toggle">
                    <i class="fa fa-credit-card"></i>
                    <span class="title">Payment Gateway</span>
                    <span class="selected"></span>
                </a>
            </li>-->
             <!-- <li class="nav-item  @if(request()->path() == 'admin/sell-log') active open @endif">
                <a href="{{route('sellLog')}}" class="nav-link nav-toggle">
                    <i class="fa fa-shopping-cart "></i>
                    <span class="title">Sell Log</span>
                </a>
            </li>-->
             <!--<li class="nav-item  @if(request()->path() == 'admin/ico') active open @endif">
                <a href="{{route('ico.index')}}" class="nav-link nav-toggle">
                    <i class="fa fa-calendar-check-o "></i>
                    <span class="title">Manage ICO</span>
                </a>
            </li>-->
            <li class="nav-item @if(request()->path() == 'admin/general') active open
            @elseif(request()->path() == 'admin/logo') active open 
            @elseif(request()->path() == 'admin/template') active open 
            @elseif(request()->path() == 'admin/sms-api') active open 
            @endif">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-cogs"></i>
                    <span class="title">Website Control</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item @if(request()->path() == 'admin/general') active open @endif">
                        <a href="{{route('general')}}" class="nav-link ">
                            <i class="fa fa-cog"></i>
                            <span class="title">General Settings</span>
                        </a>
                    </li>
                    <li class="nav-item @if(request()->path() == 'admin/logo') active open @endif">
                        <a href="{{route('logo')}}" class="nav-link ">
                            <i class="fa fa-picture-o"></i>
                            <span class="title">Logo and Icon</span>
                        </a>
                    </li>
                     <li class="nav-item @if(request()->path() == 'admin/template') active open @endif">
                        <a href="{{route('template')}}" class="nav-link ">
                            <i class="fa fa-envelope"></i>
                            <span class="title">Email Template</span>
                        </a>
                    </li>
                    <li class="nav-item @if(request()->path() == 'admin/sms-api') active open @endif">
                        <a href="{{route('sms.api')}}" class="nav-link ">
                            <i class="fa fa-envelope"></i>
                            <span class="title">SMS API</span>
                        </a>
                    </li>
                </ul>
            </li>
             <li class="nav-item  @if(request()->path() == 'admin/icosetting') active open @endif">
                <a href="{{route('icosetting')}}" class="nav-link nav-toggle">
                    <i class="fa fa-cogs"></i>
                    <span class="title">WDMD ICO setting</span>
                    <span class="selected"></span>
                </a>
            </li>
             <li class="nav-item  @if(request()->path() == 'admin/setcrowddetting') active open @endif">
                <a href="{{route('setcrowddetting')}}" class="nav-link nav-toggle">
                    <i class="fa fa-cogs"></i>
                    <span class="title">Set Crowd setting</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="nav-item  @if(request()->path() == 'admin/withdrawal_request') active open @endif">
                <a href="{{route('withdrawal_request')}}" class="nav-link nav-toggle">
                    <i class="fa fa-cogs"></i>
                    <span class="title">Withdrawal Request</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="nav-item @if(request()->path() == 'admin/faq') active open
            @elseif(request()->path() == 'admin/road') active open 
            @elseif(request()->path() == 'admin/testim') active open 
            @elseif(request()->path() == 'admin/services') active open 
            @elseif(request()->path() == 'admin/teams') active open 
            @elseif(request()->path() == 'admin/banner') active open 
            @elseif(request()->path() == 'admin/about') active open 
            @elseif(request()->path() == 'admin/subsc') active open 
            @elseif(request()->path() == 'admin/footer') active open 
            @elseif(request()->path() == 'admin/background') active open 
            @endif">
                
            </li>
        </ul>
    </li>
</ul>
</div>
</div>