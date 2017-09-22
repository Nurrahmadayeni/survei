<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Informasi Manajemen Survei | Universitas Sumatera Utara">
    <meta name="keywords" content="survei, sistem informasi manajamen survei, usu, universitas sumatera utara">
    <meta name="author" content="PSI USU">

    <title>SURVEI USU - Landing Page</title>

    <!-- Bootstrap Core CSS -->
    <link href="{{url('assets/' . 'bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{url('assets/' . 'global/plugins/bower_components/datatables/dataTables.bootstrap.css')}}" rel="stylesheet">
    <link href="{{url('assets/' . 'global/plugins/bower_components/datatables/datatables.responsive.css')}}" rel="stylesheet">

    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>

    <!-- Theme CSS -->
    <link href="{{url('/' . 'css/freelancer.min.css')}}" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{{url('assets/' . 'global/plugins/bower_components/fontawesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">


</head>

<body id="page-top" class="index">

<!-- Navigation -->
<nav id="mainNav" class="navbar navbar-default navbar-fixed-top navbar-custom">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand" href="#page-top">SURVEI</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="hidden">
                    <a href="#page-top"></a>
                </li>
                <li class="page-scroll">
                    <a href="#survei">SURVEI AKTIF</a>
                </li>
                <li class="page-scroll">
                    <a href="#about">Tentang SURVEI</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>

<!-- Header -->
<header>
    <div class="container" id="maincontent" tabindex="-1">
        <div class="row">
            <div class="col-lg-12">
                <div class="intro-text">
                    <h1 class="name">SURVEI</h1>
                    <hr class="star-light">
                    <span class="skills">Sistem Informasi Manajemen SURVEI USU</span>
                </div>
            </div>
        </div>
    </div>
</header>

{{--survei aktif--}}
<section id="survei">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>SURVEI AKTIF</h2>
                <hr class="star-primary">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive mb-20">
                    <table id="survey-active" class="table table-striped table-hover table-theme">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>No.</th>
                            <th>Judul Survei</th>
                            <th>Survei</th>
                            <th>Tujuan Survei</th>
                            <th>Jangka Waktu</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>Tentang SURVEI</h2>
                <hr class="star-primary">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-lg-offset-2">
                <p>SURVEI merupakan sistem informasi yang dirancang untuk memudahkan dalam melakukan survei secara online.</p>
            </div>
            <div class="col-lg-4">
                <p>Untuk dapat menggunakan SURVEI, dosen/pegawai/mahasiswa harus melakukan login terlebih dahulu. Silahkan klik tombol login di bawah untuk melakukan login.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 text-center">
                <a href="{{$login_link}}" class="btn btn-lg btn-primary btn-outline">
                    <i class="fa fa-sign-in"></i> Login
                </a>
            </div>
        </div>
    </div>
</section>


<!-- Footer -->
<footer class="text-center">
    <div class="footer-below">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    Copyright &copy; PSI 2017
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- jQuery -->
<script src="{{url('assets/' . 'global/plugins/bower_components/jquery/dist/jquery.min.js')}}"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{url('assets/' . 'bootstrap/js/bootstrap.min.js')}}"></script>

<script src="{{url('assets/' . 'global/plugins/bower_components/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{url('assets/' . 'global/plugins/bower_components/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{url('assets/' . 'global/plugins/bower_components/datatables/datatables.responsive.js')}}"></script>

<script src="{{url('assets/' . 'js/customize.js')}}"></script>


<!-- Plugin JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

<!-- Theme JavaScript -->
<script src="{{url('js/freelancer.min.js')}}"></script>

</body>

</html>
