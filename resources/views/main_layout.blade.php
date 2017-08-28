<!DOCTYPE html>
<html lang="en">

    <!-- START @HEAD -->
    <head>
    <!-- START @META SECTION -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="Sistem Informasi Manajemen Survey Universitas Sumatera Utara">
        <meta name="keywords" content="survey, sistem informasi manajamen survey, usu, universitas sumatera utara">
        <meta name="author" content="PSI USU">
        <title>SURVEY USU - {{$page_title}}</title>
        <!--/ END META SECTION -->

    <!-- START @FAVICONS -->
        <link rel="icon" type="image/png" href="{{url('img/logo.png')}}"/>
        <!--/ END FAVICONS -->

    <!-- START @FONT STYLES -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet">
        <!--/ END FONT STYLES -->

    <!-- START @GLOBAL MANDATORY STYLES -->
        @if(!empty($css['globals']))
            @foreach($css['globals'] as $global)
                <link href="{{url('assets/' . $global)}}" rel="stylesheet">
            @endforeach
        @endif
    <!--/ END GLOBAL MANDATORY STYLES -->

    <!-- START @PAGE LEVEL STYLES -->
        @if(!empty($css['pages']))
            @foreach($css['pages'] as $page)
                <link href="{{url('assets/' . $page)}}" rel="stylesheet">
            @endforeach
        @endif
    <!--/ END PAGE LEVEL STYLES -->

    <!-- START @THEME STYLES -->
        @if(!empty($css['themes']))
            @foreach($css['themes'] as $key=>$theme)
                @if(is_array($theme))
                    <link href="{{url('assets/' . $key)}}" rel="stylesheet" id="{{$theme['id']}}">
                @else
                    <link href="{{url('assets/' . $theme)}}" rel="stylesheet">
                @endif
            @endforeach
        @endif
    <!--/ END THEME STYLES -->
    </head>
    <!--/ END HEAD -->

    <body class="page-sound">

        @if($page_title !== 'Login')
            @include('layout.header')
            @include('layout._sidebar_left')
        @endif

        @yield('content')

        @if($page_title !== 'Login')
            @include('layout._sidebar_right')
        @endif

    <!-- START JAVASCRIPT SECTION (Load javascripts at bottom to reduce load time) -->
    <!-- START @CORE PLUGINS -->
        @if(!empty($js['cores']))
            @foreach($js['cores'] as $core)
                <script src="{{url('assets/' . $core)}}"></script>
            @endforeach
        @endif
    <!--/ END CORE PLUGINS -->

    <!-- START @PAGE LEVEL PLUGINS -->
        @if(!empty($js['additionalScripts']))
            @foreach($js['additionalScripts'] as $script)
                <script src="{{$script}}"></script>
            @endforeach
        @endif

        @if(!empty($js['plugins']))
            @foreach($js['plugins'] as $plugin)
                <script src="{{url('assets/' . $plugin)}}"></script>
            @endforeach
        @endif
    <!--/ END PAGE LEVEL PLUGINS -->

    <!-- START @PAGE LEVEL SCRIPTS -->
        @if(!empty($js['scripts']))
            @foreach($js['scripts'] as $script)
                <script src="{{url('assets/' . $script)}}"></script>
            @endforeach
        @endif
    <!--/ END PAGE LEVEL SCRIPTS -->

    @php
        if(!isset($sessions))
        {
            foreach (['danger', 'warning', 'success', 'info'] as $msg)
            {
                if(Session::has('alert-' . $msg))
                {
                    $sessions['alert-' .$msg] = Session::pull('alert-' . $msg);
                }
            }
        }
    @endphp
    <script src="{{url('js/bootstrap-notify.min.js')}}"></script>
    <script>
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(isset($sessions['alert-' . $msg]))
                $.notify({
            message: "{{$sessions['alert-' . $msg]}}"
        },{
            type: "{{$msg}}",
            placement: {
                from: "bottom"
            },
            animate: {
                enter: "animated fadeInRight",
                exit: "animated fadeOutRight"
            }
        })
        @endif
    @endforeach

    @if(isset($errors) && $errors->has('alert-danger'))
        @foreach($errors->get("alert-danger") as $error)
            $.notify({
            message: "{{$error}}"
        },{
            type: "danger",
            placement: {
                from: "bottom"
            },
            animate: {
                enter: "animated fadeInRight",
                exit: "animated fadeOutRight"
            }
        })
        @endforeach
        @endif
    </script>

    <!--/ END JAVASCRIPT SECTION -->

    </body>
    <!-- END BODY -->

</html>