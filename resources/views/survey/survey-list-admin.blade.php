<div class="col-md-12">
    <div class="pull-left">
        <a href="{{url('survey/create')}}" class="btn btn-theme rounded"
           data-toggle="tooltip" data-placement="top" title="Tambah">
            <i class="fa fa-plus"></i> Tambah Survey
        </a>
    </div>
    <div class="clearfix"></div>
    <hr>
</div>
<input type="hidden" id="auths" value="{{$auths}}">
{{csrf_field()}}
<div class="col-md-12">
    <div class="table-responsive mb-20">
        <table id="survey-list-admin" class="table table-striped table-theme">
            <thead>
            <tr>
                <th>ID</th>
                <th>No.</th>
                <th>Judul Survei</th>
                @if($auths=='SU')
                    <th>Unit Survei</th>
                @endif
                <th>Dibuat Oleh</th>
                <th>Sampel</th>
                <th>Jumlah Sampel</th>
                <th>Jangka Waktu</th>
                <th>Pertanyaan</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>