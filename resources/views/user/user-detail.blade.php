@extends('main_layout')

@php
    if(!isset($user_auth))
    {
        $user_auth = new \App\UserAuth();
    }
    $olds = session()->getOldInput();

    foreach ($olds as $key => $old)
    {
        if($key !== '_token')
        {
            $user_auth[$key] = old($key);
        }
    }

    if(!empty($olds['auth_type']))
    {
        $user_auths = new \Illuminate\Support\Collection();
        foreach ($olds['auth_type'] as $key => $item)
        {
            $user_auth['auth_type'] = $item;
            $user_auth['unit'] = $olds['unit'][$key];
            $user_auths->push($user_auth);
        }
    }

    if(!isset($user_auths))
    {
        $user_auths = new \Illuminate\Support\Collection();
    }
@endphp

@section('content')
    <!-- START @PAGE CONTENT -->
    <section id="page-content">

        <!-- Start page header -->
        <div id="tour-11" class="header-content">
            <h2><i class="fa fa-user-circle-o"></i>{{$page_title}}</h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label">Direktori Anda:</span>
                <ol class="breadcrumb">
                    <li class="active">User > {{$upd_mode == 'create' ? 'Tambah' : 'Ubah'}}</li>
                </ol>
            </div>
        </div><!-- /.header-content -->
        <!--/ End page header -->

        <!-- Start body content -->
        <div class="body-content animated fadeIn">
            <div class="panel rounded shadow">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h3 class="panel-title">{{$upd_mode == 'create' ? 'Tambah' : 'Ubah'}} User</h3>
                    </div>
                    <div class="pull-right">
                        <button class="btn btn-sm" data-action="collapse" data-container="body" data-toggle="tooltip"
                                data-placement="top" data-title="Collapse"><i class="fa fa-angle-up"></i></button>
                    </div>
                    <div class="clearfix"></div>
                </div><!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{url($action_url)}}" method="post">
                                <div class="form-group {{$errors->has('username') ? 'has-error' : null}}">
                                    <label for="username" class="control-label">Username (NIP / Nama sesuai
                                        SIMSDM)</label>
                                    <input name="username_display" type="text" class="form-control search-employee"
                                           value="{{$user_auth['username_display']}}" required {{$upd_mode == 'create' ? null : 'disabled'}}>
                                    <input name="username" type="hidden" value="{{$user_auth['username']}}">
                                    @if($errors->has('username'))
                                        <label id="bv_required-error" class="error" for="bv_required"
                                               style="display: inline-block;">
                                            {{$errors->first('username')}}
                                        </label>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="full_name" class="control-label">Nama Lengkap</label>
                                    <input name="full_name" type="text" class="form-control"
                                           value="{{$user_auth['full_name']}}" readonly>
                                </div>

                                @if($upd_mode != "display")
                                    <div class="form-group">
                                        <a href="#" class="btn btn-theme btn-md rounded table-add" title="Tambah"><i
                                                    class="fa fa-plus"></i></a>
                                    </div>
                                @endif

                                <div id="user-auth-table" class="form-group table-responsive user-auth-table">
                                    <table class="table">
                                        <thead>
                                        <th class="text-center">Otorisasi</th>
                                        <th class="text-center">Unit/Subunit</th>
                                        <th class="text-center">Hapus</th>
                                        </thead>
                                        <tbody>
                                        @foreach($user_auths as $item)
                                            <tr class="text-center">
                                                <td>
                                                    <select name="auth_type[]" type="text" class="form-control select2">
                                                        <option value="OPU" {{$item['auth_type'] == 'OPU' ? 'selected' : null}}>Operator Unit</option>
                                                        <option value="OPF" {{$item['auth_type'] == 'OPF' ? 'selected' : null}}>Operator Fakultas</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="unit[]" type="text" class="form-control select2">
                                                        @foreach($units as $unit)
                                                            <option value="{{$unit['code']}}" {{$item['unit'] == $unit['code'] ? 'selected' : null}}>{{$unit['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    @if($upd_mode != "display")
                                                        <a href="#" class="table-remove btn btn-danger rounded"><i
                                                                    class="fa fa-trash"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="hide text-center">
                                            <td>
                                                <select name="auth_type[]" type="text" class="form-control" value=""
                                                        disabled>
                                                    <option value="OPU">Operator Unit</option>
                                                    <option value="OPF">Operator Fakultas</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="unit[]" type="text" class="form-control" value=""
                                                        disabled>
                                                    @foreach($units as $unit)
                                                        <option value="{{$unit['code']}}">{{$unit['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                @if($upd_mode != "display")
                                                    <a href="#" class="table-remove btn btn-danger rounded"><i
                                                                class="fa fa-trash"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                @if($upd_mode == 'edit')
                                    <input type="hidden" name="_method" value="PUT">
                                @endif
                                {{csrf_field()}}

                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <button class="btn btn-success rounded" type="submit">Submit</button>
                                                <a href="{{url('partners')}}" class="btn btn-danger rounded">Batal</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div><!-- /.col-md-12 -->
                    </div><!-- /.row -->
                </div><!-- /.panel-body -->
            </div><!-- /.panel -->
        </div><!-- /.body-content -->
        <!--/ End body content -->

        <!-- Start footer content -->
    @include('layout.footer')
    <!--/ End footer content -->

    </section><!-- /#page-content -->
    <!--/ END PAGE CONTENT -->
@endsection