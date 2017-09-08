@extends('main_layout')

@php
    $olds = session()->getOldInput();

@endphp

@section('content')
    <!-- START @PAGE CONTENT -->
    <section id="page-content">

        <!-- Start page header -->
        <div id="tour-11" class="header-content">
            <h2><i class="fa fa-list-alt"></i>Survei</h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label">Direktori Anda:</span>
                <ol class="breadcrumb">
                    <li class="active">Survey > Jawab</li>
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
                            <form id="form_question" method="post" enctype="multipart/form-data">
                                @php $no=1; @endphp
                                @foreach($questions as $question)
                                    <div class='panel rounded shadow panel-theme' style="margin-bottom: 2%">
                                        <div class='panel-heading rounded' style="padding: 1%">
                                            {{$no}}. {{$question->question }}
                                        </div>
                                        <div class='panel-body' style='background-color:#F4F4F4'>
                                            <input type='hidden' name='survey_id' value='{{$question->survey_id}}'>
                                            {{csrf_field()}}
                                            <input type='hidden' name='qst_id[]' value='{{$question->id}}'>
                                            <input type='hidden' name='answer_type[]' value='{{$question->answer_type}}'>

                                            @if($question->answer_type=='1')
                                                @php $val = explode(', ', $question->choices); @endphp
                                                    @for($i=0; $i<=count($val)-1; $i++)
                                                        <div class='rdio radio-inline rdio-theme rounded'>
                                                            <input class='radio-inline' id='answerR{{$no}}{{$i}}' required type='radio' name='answer[{{$question->id}}]' value='{{$val[$i]}}'>
                                                            <label for='answerR{{$no}}{{$i}}'>{{$val[$i]}}</label>
                                                        </div>
                                                        <br/>
                                                    @endfor
                                           @elseif($question->answer_type=='2')
                                                @php $val = explode(', ', $question->choices); @endphp
                                                @for($i=0; $i<=count($val)-1; $i++)
                                                    <div class='ckbox ckbox-theme'>
                                                        <input id='answerC{{$no}}{{$i}}' class='sampel' type='checkbox' name='chosen[{{$question->id}}][]' value='{{$val[$i]}}'>
                                                        <label for='answerC{{$no}}{{$i}}' class='control-label'>{{$val[$i]}}</label>
                                                    </div>
                                                @endfor
                                           @elseif($question->answer_type=='3')
                                                <input type='number' class='number form-control' name='answer[{{$question->id}}]' placeholder='input angka' required>
                                           @elseif($question->answer_type=='4')
                                                <textarea class='form-control' rows='3' name='answer[{{$question->id}}]' required></textarea>
                                           @endif
                                        </div>
                                    </div>
                                <hr>
                                    @php $no++; @endphp
                                @endforeach

                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                @if($disabled == null)
                                                    <button id="survey-submit" class="btn btn-success rounded btn-stroke btn-slideright"
                                                            type="submit">Submit
                                                    </button>
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