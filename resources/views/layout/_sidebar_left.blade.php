<!-- START @SIDEBAR LEFT -->
<aside id="sidebar-left" class="sidebar-circle">
    <!-- Start left navigation - profile shortcut -->
    <div id="tour-8" class="sidebar-content">
        <div class="media">
            <a class="pull-left has-notif avatar" href="{{url('user/profile')}}">
                <img src="{{$user_info['photo']}}" alt="admin">
                <i class="online"></i>
            </a>
            <div class="media-body">
                <h4 class="media-heading">Hello, <span id='username'>{{$user_info['full_name']}}</span></h4>
                <small>NIP : {{$user_info['username']}}</small>
            </div>
        </div>
    </div><!-- /.sidebar-content -->
    <!--/ End left navigation -  profile shortcut -->

    <!-- Start left navigation - menu -->
    <ul id="tour-9" class="sidebar-menu">

        <!-- Start navigation - dashboard -->
        <li class="submenu {!! Request::is('/','survey', 'survey/*') ? 'active' : null !!}">
            <a href="{{url('/survey')}}">
                <span class="icon"><i class="fa fa-list-alt"></i></span>
                <span class="text">Survei</span>
                {!! Request::is('/','survey', 'survey/*') ? '<span class="selected"></span>' : null !!}
            </a>
        </li>
        @can('admin-menu')
            <li class="submenu {!! Request::is('survey/report', 'survey/report') ? 'active' : null !!}">
                <a href="{{url('survey/report')}}">
                    <span class="icon"><i class="fa fa-line-chart"></i></span>
                    <span class="text">Laporan Survei</span>
                    {!! Request::is('survey/report', 'survey/report') ? '<span class="selected"></span>' : null !!}
                </a>
            </li>
        @endcan
        @can('SU-menu')
            <li class="submenu {!! Request::is('users', 'users/*') ? 'active' : null !!}">
                <a href="javascript:void(0);">
                    <span class="icon"><i class="fa fa-lock"></i></span>
                    <span class="text">Admin </span>
                    <span class="arrow"></span>
                    {!! Request::is('users', 'users/*') ? '<span class="selected"></span>' : null !!}
                </a>
                <ul>
                    <li><a href="{{url('users')}}">User</a></li>
                </ul>
            </li>
    @endcan

    <!--/ End navigation - dashboard -->
    </ul>

</aside><!-- /#sidebar-left -->
<!--/ END SIDEBAR LEFT -->