@extends('main_layout')

@php
    $olds = session()->getOldInput();

    if(!isset($survey_objs)){
        $survey_objs = new \Illuminate\Database\Eloquent\Collection();
    }

    if(!isset($survey)){
        $survey = new \App\Survey();
    }

    foreach ($olds as $key => $old){
        if($key !== '_token' && $key!='sample' && $key!='unit_objectives'){
            $survey[$key] = old($key);
        }
    }

    $i = 0;
    while(old('sample.'.$i)){
        $survey->sample = new \Illuminate\Support\Collection();
        $survey->sample->push(old('sample.'.$i));
        $i++;
    }

    $j=0;
    if(old('unit_objectives.0'))
        $survey_objs = new \Illuminate\Database\Eloquent\Collection();

    while(old('unit_objectives.'.$j)){
        $survey_obj = new \App\SurveyObjective();
        $survey_obj['objective'] = old('unit_objectives.' . $j);
        $survey_objs->push($survey_obj);
        $j++;
    }

@endphp

@section('content')
    <!-- START @PAGE CONTENT -->
    <section id="page-content">

        <!-- Start page header -->
        <div id="tour-11" class="header-content">
            <h2><i class="fa fa-list-alt"></i>Survey</h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label">Direktori Anda:</span>
                <ol class="breadcrumb">
                    <li class="active">Survey > Tambah</li>
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
                                <button class="btn btn-sm" data-action="collapse" data-container="body"
                                        data-toggle="tooltip"
                                        data-placement="top" data-title="Collapse"><i class="fa fa-angle-up"></i>
                                </button>
                            </div>
                            <div class="clearfix"></div>
                        </div><!-- /.panel-heading -->
                        <div id="survey-container" class="panel-body">
                            @if($upd_mode == 'show')
                                <div class="form-group">
                                    <a href="{{url('survey/edit/'.$survey->id)}}"
                                       class="btn btn-success rounded">Ubah</a>
                                    <a href="{{url('/')}}" class="btn btn-danger rounded">Batal</a>
                                </div>
                            @endif
                                
                            @if($upd_mode == 'create')
                                <form id="form_addSurvey" method="post" enctype="multipart/form-data">
                            @elseif($upd_mode == 'copy')
                                <form id="form_copySurvey" method="post" enctype="multipart/form-data">
                            @elseif($upd_mode == 'edit')
                                <form action="{{url($action_url)}}" method="post" enctype="multipart/form-data">
                            @endif
                                @if($upd_mode != 'create')
                                    <input name="id" type="hidden" value="{{$survey['id']}}">
                                @endif
                                <input name="auth" type="hidden" value="{{$auth}}">
                                <input name="upd_mode" type="hidden" value="{{$upd_mode}}" disabled>
                                @include('layout.input-text', ['passing_variable' => 'title', 'passing_description' => 'Judul Survey'])
                                @include('layout.input-date', ['passing_variable' => 'start_date', 'passing_description' => 'Tanggal Mulai Survey'])
                                @include('layout.input-date', ['passing_variable' => 'end_date', 'passing_description' => 'Tanggal Berakhir Survey'])

                                <div id="unit" class="form-group">
                                    <label for="unit" class="control-label">Pilih Unit Survey</label>
                                    <select class="form-control mb-15 select2" name='unit' id="pilihan" data-placeholder="-- Pilih Unit Survey --" {{$disabled}} required>
                                        <option value="" disabled="">-- Pilih Unit Survey --</option>
                                        @foreach($units as $unit)
                                            @if(!empty($unit['code']))
                                                <option value="{{$unit['code']}}" {{$survey['unit'] == $unit['code'] ? "selected" : null}}>{{$unit['name']}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                @if($auth != 'OPF')
                                    <div id="unit_objectives" class="form-group">
                                        <label for="unit_objectives" class="control-label">Pilih Tujuan Survey</label>
                                        <select class="form-control mb-15 select2" multiple="multiple" name='unit_objectives[]' id="pilihan_tujuan" data-placeholder="-- Pilih Tujuan Survey --" {{$disabled}} required>
                                            @foreach($units as $key=>$unit)
                                                @if(!empty($unit['code']))
                                                    <option value="{{$unit['code']}}"
                                                        {{$survey_objs->contains('objective',$unit['code']) == true ? 'selected' : null }}
                                                        >{{$unit['name']}}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button type='button' class='btn btn-success btn-xs btn-slideright selectall'>Select All Unit Kerja</button>
                                        <button type='button' class='btn btn-danger btn-xs btn-slideright deselectall'>Delect All Unit Kerja</button>
                                    </div>
                                @endif

                                <div id="is_subjective" class="form-group">
                                    <label for="is_subjective" class="control-label">Apakah survey matakuliah ? </label>
                                    <div>
                                        <div class='rdio radio-inline rdio-theme rounded'>
                                            <input type='radio' class='radio-inline' id='radio-type-rounded1' required  value='1' {{$disabled}} {{$survey['is_subject'] == '1' ? "checked" : null}} name="is_subject">
                                            <label class='is_subject' for='radio-type-rounded1'>YA</label>
                                        </div>
                                        <div class='rdio radio-inline rdio-theme rounded'>
                                            <input type='radio' class='radio-inline' id='radio-type-rounded2' required  value='0' {{$disabled}} name="is_subject" {{$survey['is_subject'] == '0' ? "checked" : null}}>
                                            <label class='is_subject' for='radio-type-rounded2'>TIDAK</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id='subject' {{$survey['is_subject'] == '1' ? null : "style=display:none" }}>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="name-sample" class="control-label">Tahun Ajaran</label>
                                            <select class="form-control mb-15 select2" name='academic_year' id="pilihan" data-placeholder="-- Pilih Unit Survey --" {{$disabled}} required>
                                                <option value="" disabled="">-- Pilih Tahun Ajaran --</option>
                                                @foreach($academic_years as $academic_year)
                                                    <option value="{{$academic_year}}" {{$survey['academic_year'] == $academic_year ? "selected" : null}}>{{$academic_year}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="name-sample" class="control-label">Semester </label>
                                            <select class="form-control mb-15 select2" name='semester' id="pilihan" data-placeholder="-- Pilih Unit Survey --" {{$disabled}} required>
                                                <option value="" disabled="">-- Pilih Semester --</option>
                                                <option value="1" {{$survey['semester'] == 1 ? "selected" : null}}>Ganjil</option>
                                                <option value="2" {{$survey['semester'] == 2 ? "selected" : null}}>Genap</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id='sample' {{$survey['is_subject'] == '0' ? null : "style=display:none" }}>
                                    <label for="name-sample" class="control-label">Sampel </label>
                                    <div class="ckbox ckbox-theme">
                                        <input id="mhs" class="sample" type="checkbox" name="sample[]" value="student"
                                        {{$survey['sample']->contains('student') == 1 ? 'checked' : null }} {{$disabled}}>
                                        {{--@if(is_array($survey['sample']) && in_array('student', $survey['sample']))--}}
                                            {{--{{'checked'}}@else {{'null'}} @endif--}}
                                        {{-->--}}
                                        <label for="mhs" class="control-label">Mahasiswa</label>
                                    </div>
                                    <div class="ckbox ckbox-theme">
                                        <input id="dsn" class="sample" type="checkbox" name="sample[]" value="lecture"
                                        {{$survey['sample']->contains('lecture') == 1 ? 'checked' : null }} {{$disabled}}>
                                        {{--@if(is_array($survey['sample']) && in_array('lecture', $survey['sample']))--}}
                                            {{--{{'checked'}}@else {{'null'}} @endif--}}
                                        {{-->--}}
                                        <label for="dsn" class="control-label">Dosen</label>
                                    </div>
                                    <div class="ckbox ckbox-theme">
                                        <input id="pgw" class="sample" type="checkbox" name="sample[]" value="employee"
                                        {{$survey['sample']->contains('employee') == 1 ? 'checked' : null }} {{$disabled}}>
                                        {{--@if(is_array($survey['sample']) && in_array('employee', $survey['sample']))--}}
                                            {{--{{'checked'}}@else {{'null'}} @endif--}}
                                        {{-->--}}
                                        <label for="pgw" class="control-label">Pegawai</label>
                                    </div>
                                </div>

                                @if($upd_mode == 'edit')
                                    <input type="hidden" name="_method" value="PUT">
                                @endif
                                {{csrf_field()}}

                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                @if($disabled == null)
                                                    @if($upd_mode == 'create')
                                                        <button id="survey-submit" class="btn btn-success rounded btn-stroke btn-slideright"
                                                                type="submit">Submit
                                                        </button>
                                                    @elseif($upd_mode == 'copy')
                                                        <button id="survey-copy" class="btn btn-success rounded btn-stroke btn-slideright"
                                                                type="submit">Submit
                                                        </button>
                                                    @elseif($upd_mode == 'edit')
                                                        <button id="survey-update" class="btn btn-success rounded btn-stroke btn-slideright"
                                                                type="submit">Submit
                                                        </button>
                                                    @endif

                                                    <a href="{{url('/survey')}}" class="btn btn-danger rounded btn-stroke btn-slideright">Batal</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div><!-- /.panel -->
                    </div><!-- /.body-content -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div>
        <!--/ End body content -->

        <!-- Start footer content -->
    @include('layout.footer')
    <!--/ End footer content -->

    </section><!-- /#page-content -->
    <!--/ END PAGE CONTENT -->
@endsection