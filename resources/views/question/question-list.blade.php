@extends('main_layout')

@php
    $olds = session()->getOldInput();

    foreach ($olds as $key => $old){
        if($key !== '_token' && $key!='sample' && $key!='unit_objectives'){
            $question[$key] = old($key);
        }
    }
@endphp

@section('content')
    <!-- Modal for delete question -->
    <div id="delete" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Hapus Pertanyaan</h4>
                </div>
                <div class="modal-body">
                    <p>Apakah anda yakin ingin menghapus data ini?</p>
                </div>
                <div class="modal-footer">
                    <form action="" method="post">
                        <input type="hidden" name="_method" value="delete">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-danger rounded">OK</button>
                        <button type="button" class="btn btn-default rounded" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Ubah Pertanyaan</h4>
                </div>
                <div class="modal-body">
                    <form method='post' action=''>
                        <input type='hidden' name='qst_id' class='form-control' id="qst_id">
                        {{csrf_field()}}
                        <input type='text' class='form-control' name='question' id="question">
                        <div class='modal-footer'>
                            <input type='submit' value='Update' class='btn btn-theme rounded' id='update'>
                            <button class='btn btn-default rounded' data-dismiss='modal'>Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- START @PAGE CONTENT -->
    <section id="page-content">
        <!-- Start page header -->
        <div id="tour-11" class="header-content">
            <h2><i class="fa fa-list-alt"></i>Pertanyaan</h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label">Direktori Anda:</span>
                <ol class="breadcrumb">
                    <li class="active">Pertanyaan > Daftar</li>
                </ol>
            </div>
        </div><!-- /.header-content -->
        <!--/ End page header -->

        <!-- Start body content -->
        <div class="body-content animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel rounded shadow">
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h3 class="panel-title">{{$page_title}}</h3>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-sm" data-action="collapse" data-container="body" data-toggle="tooltip" data-placement="top" data-title="Collapse"><i class="fa fa-angle-up"></i>
                                </button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div id="question-container" class="panel-body">
                            <div class="col-md-12">
                                <div class="pull-left">
                                    <a href="{{url('question/create/'.$survey['id'])}}" class="btn btn-theme rounded tambah"
                                       data-toggle="tooltip" data-placement="top" title="Tambah">
                                        <i class="fa fa-plus"></i> Tambah Pertanyaan
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                                <hr>
                            </div>
                            <input type="hidden" id='id_survey' value='{{$survey['id']}}'>
                            {{csrf_field()}}
                            <div class="col-md-12">
                                <div class="table-responsive mb-20">
                                <table id="question-list" class="table table-striped table-theme">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>No.</th>
                                        <th>Pertanyaan</th>
                                        <th>Jenis Pilihan Jawaban</th>
                                        <th>Pilihan</th>
                                        <th>Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layout.footer')

    </section>
    <!--/ END PAGE CONTENT -->
@endsection