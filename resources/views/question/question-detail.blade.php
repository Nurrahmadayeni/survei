@extends('main_layout')

@php
    $olds = session()->getOldInput();

    foreach ($olds as $key => $old){
        if($key !== '_token' && $key!='sample' && $key!='unit_objectives'){
            $question[$key] = old($key);
        }
    }
@endphp

<div class="modal fade modal-theme" id="style_answer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog vertical-align-center modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id ="myModalLabel">Jenis Pilihan Jawaban</h4>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-theme">
                    <thead>
                    <tr>
                        <th>Jenis Jawaban</th>
                        <th>Contoh</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($types as $type)
                        <tr>
                            <td>{{$type['type']}}</td>
                            <td>
                                @if($type['id']=='1')
                                    urutan dari terendah
                                    <br/>1.
                                    <div class='rdio radio-inline rdio-theme rounded'>
                                        <input id='radio-primary' type='radio' name='radio'>
                                        <label for='radio-primary'>Pilihan 1</label>
                                    </div>
                                    <div class='rdio radio-inline rdio-theme rounded'>
                                        <input id='radio-primary2' type='radio' name='radio'>
                                        <label for='radio-primary2'>Pilihan 2</label>
                                    </div>
                                    <div class='rdio radio-inline rdio-theme rounded'>
                                        <input id='radio-primary3' type='radio' name='radio' required >
                                        <label for='radio-primary3'>Pilihan 3</label>
                                    </div>
                                    <div class='rdio radio-inline rdio-theme rounded'>
                                        <input id='radio-primary4' type='radio' name='radio' required >
                                        <label for='radio-primary4'>Pilihan 4</label>
                                    </div>
                                    <br/>2.
                                    <div class='rdio radio-inline rdio-theme rounded'>
                                        <input id='radio-primary5' type='radio' name='radio'>
                                        <label for='radio-primary5'>Ya</label>
                                    </div>
                                    <div class='rdio radio-inline rdio-theme rounded'>
                                        <input id='radio-primary6' type='radio' name='radio'>
                                        <label for='radio-primary6'>Tidak</label>
                                    </div>
                                @endif

                                @if($type['id']=='2')
                                    <div class='ckbox ckbox-theme'>
                                        <input id='1' class='sampel' type='checkbox' name='sampel[]' value='mhs'>
                                        <label for='1' class='control-label'>Pilihan 1</label>
                                    </div>
                                    <div class='ckbox ckbox-theme'>
                                        <input id='2' class='sampel' type='checkbox' name='sampel[]' value='dsn'>
                                        <label for='2' class='control-label'>Pilihan 2</label>
                                    </div>
                                    <div class='ckbox ckbox-theme'>
                                        <input id='3' class='sampel' type='checkbox' name='sampel[]' value='pgw'>
                                        <label for='3' class='control-label'>Pilihan 3</label>
                                    </div>
                                @endif
                                @if($type['id']=='3')
                                    <input type='number' class='form-control' value='input angka' required>
                                @endif
                                @if($type['id']=='4')
                                    <textarea class='form-control' rows='2'></textarea>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-theme" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@section('content')
    <!-- START @PAGE CONTENT -->
    <section id="page-content">

        <!-- Start page header -->
        <div id="tour-11" class="header-content">
            <h2><i class="fa fa-list-alt"></i>Pertanyaan</h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label">Direktori Anda:</span>
                <ol class="breadcrumb">
                    <li class="active">Pertanyaan > Tambah</li>
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
                        <div id="survey-container" class="panel-body">
                            <div class="row">
                                <h4><div class="col-md-2"><label for="title" class="control-label">Judul Survey :</label></div>
                                <div class="col-md-4"><label for="title" class="control-label"><b>{{$survey['title']}}</b></label></div></h4>
                            </div><br/>
                            <div class="row">
                                <div class="col-md-4">
                                    <label> Tambah Pertanyaan Baru <small class='text-danger'><i><b>( Pertanyaan ke <span id='jlh_tanya'>{{$question_total}} </span> )</b></i></small></label>
                                </div>
                            </div>
                            <div class="row tab-content">
                                <div class="tab-pane fade in active" id="tambah_qst">
                                    <form id='form_addQst' method="post" enctype="multipart/form-data">
                                        <input type="hidden" id='survey_id' name='survey_id' value='{{$survey['id']}}'>
                                        {{csrf_field()}}
                                        <div class="row">
                                            <div class="col-md-2">Pertanyaan</div>
                                            <div class="col-md-10">
                                                <textarea class="form-control" id="input_qst" name="question" rows="2" placeholder="Masukkan Pertanyaan" required></textarea>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="row">
                                            <div class="col-md-2">Jenis Pilihan Jawaban</div>
                                            <div class="col-md-9">
                                                <select id="answer_types" width="100%" class="form-control" placeholder="-- Pilih Jenis Pilihan Jawaban --" name="answer_type" required>
                                                    <option value='0' selected disabled >-- Pilih Jenis Pilihan Jawaban --</option>'";
                                                    @foreach($types as $type)
                                                        <option value="{{$type['id']}}">{{$type['type']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <a href="#style_answer" data-toggle='modal' class="btn btn-theme btn-sm pull-right"><span data-toggle='tooltip' data-placement='top' title='Klik Untuk Lihat Contoh Jenis Pilihan Jawaban' ><i class="fa fa-question-circle" aria-hidden="true"></i> Contoh</span></a>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="row" id="jumlah_skala_ans"></div><br/>
                                        <div class="row" id="value_ans"></div>
                                        <div class='modal-footer'>
                                            <button class="btn btn-theme rounded btn-stroke btn-slideright" type="submit" id="save_add" data-toggle='tooltip' data-placement='top' title='Simpan & Tambah Pertanyaan Lagi' >Simpan dan Tambah</button>
                                            <button class="btn btn-theme rounded btn-stroke btn-slideright" type="submit" id="save_list" data-toggle='tooltip' data-placement='top' title='Simpan & Lihat Daftar Pertanyaan'>Simpan dan Lihat Daftar</button>
                                            <a href="{{url('/survey')}}" class="btn btn-danger rounded btn-stroke btn-slideright" data-toggle='tooltip' data-placement='top' title='Batal Tambah Pertanyaan'>Batal</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @include('layout.footer')


    </section><!-- /#page-content -->
    <!--/ END PAGE CONTENT -->
@endsection