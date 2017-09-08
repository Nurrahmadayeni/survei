/**
 * Created by Yeni on 04/08/2017.
 */
function notify(message, type){
    $.notify({
        message: message
    },{
        type: type,
        placement: {
            from: "bottom"
        },
        animate: {
            enter: "animated fadeInRight",
            exit: "animated fadeOutRight"
        }
    })
}

function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

$(document).ready(function () {
    var getUrl = window.location,
        baseUrl = getUrl.protocol + "//" + getUrl.host + "/";

    if ($("#survey-list-admin").length) {
        var auths = $('#auths').val();
        if(auths=='SU'){
            var target = '1, 4, 5, 6, 7, 8, 9';
            var target_col_qst = 8;
            var target_col_act = 9;
        }else{
            var target = '1, 4, 5, 6, 7, 8';
            var target_col_qst  = 7;
            var target_col_act = 8;
        }

        var adminDatatable = $("#survey-list-admin").dataTable({
            autoWidth: false,
            responsive: true,
            ajax: baseUrl + 'survey/ajax',
            columnDefs: [
                {
                    orderable: false,
                    defaultContent: '<a data-toggle="tooltip" data-placement="top" title="Lihat Pertanyaan"><button class="btn btn-theme btn-sm btn-slideright rounded viewQst"><i class="fa fa-eye" style="color:white;"></i></button></a>' +
                    '<a data-toggle="tooltip" data-placement="top" title="Tambah Pertanyaan"><button class="btn btn-primary btn-sm btn-slideright rounded addQst"><i class="fa fa-plus" style="color:white;"></i></button></a>',
                    targets: target_col_qst
                },
                {
                    orderable: false,
                    defaultContent: '<a data-toggle="tooltip" data-placement="top" title="Salin Survey"><button class="btn btn-theme btn-sm rounded copy"><i class="fa fa-files-o" style="color:white;"></i></button></a>' +
                    '<a data-toggle="tooltip" data-placement="top" title="Ubah Survey"><button class="btn btn-primary btn-sm rounded edit"><i class="fa fa-pencil" style="color:white;"></i></button></a>' +
                    '<a data-toggle="tooltip" data-placement="top" title="Hapus Survey"><button class="btn btn-danger btn-sm rounded delete" data-toggle="modal" data-target="#delete"><i class="fa fa-times"></i></button></a>',
                    targets: target_col_act
                },
                {
                    className: "dt-center",
                    targets: [target]
                },
                {
                    width: "1%",
                    targets: [1,5]
                },
                {
                    visible: false,
                    targets: 0,
                }
            ],
        });

        $(document).on("click", "#survey-list-admin a button.copy", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = adminDatatable.fnGetPosition(target_row);
            }
            var id = adminDatatable.fnGetData(position)[0];

            window.open(baseUrl + "survey/copy/" + id, "_self");
        });

        $(document).on("click", "#survey-list-admin a button.delete", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");
            
            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = adminDatatable.fnGetPosition(target_row);
            }
            var id = adminDatatable.fnGetData(position)[0];

            $("#delete form").attr("action", baseUrl + "survey/delete?id=" + id);
        });

        $(document).on("click", "#survey-list-admin a button.edit", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = adminDatatable.fnGetPosition(target_row);
            }
            var id = adminDatatable.fnGetData(position)[0];

            window.open(baseUrl + "survey/show/" + id , "_self");
        });

        $(document).on("click", "#survey-list-admin a button.addQst", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = adminDatatable.fnGetPosition(target_row);
            }
            var id = adminDatatable.fnGetData(position)[0];

            window.open(baseUrl + "question/create/" + id, "_self");
        });

        $(document).on("click", "#survey-list-admin a button.viewQst", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = adminDatatable.fnGetPosition(target_row);
            }
            var id = adminDatatable.fnGetData(position)[0];

            window.open(baseUrl + "question/show/" + id, "_self");
        });

    }

    if ($("#survey-list-user").length) {
        var surveyUserDatatable = $("#survey-list-user").dataTable({
            autoWidth: false,
            responsive: true,
            ajax: baseUrl + 'survey/ajaxSurvey',
            columnDefs: [
                {
                    orderable: false,
                    defaultContent: '<a data-toggle="tooltip" data-placement="top" title="Jawab Survey"><button class="btn btn-theme btn-sm rounded answer"><i class="fa fa-eye" style="color:white;"></i></button></a>',
                    targets: 5
                },
                {
                    className: "dt-center",
                    targets: [1, 3, 4, 5]
                },
                {
                    width: "5%",
                    targets: 1
                },
                {
                    visible: false,
                    targets: 0
                }
            ],
        });

        $(document).on("click", "#survey-list-user a button.answer", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = surveyUserDatatable.fnGetPosition(target_row);
            }
            var id = surveyUserDatatable.fnGetData(position)[0];

            window.open(baseUrl + "survey/answer/" + id, "_self");
        });
    }

    if ($("#user-list").length) {
        var userDatatable = $("#user-list").dataTable({
            autoWidth: false,
            responsive: true,
            ajax: baseUrl + 'users/ajax',
            columnDefs: [
                {
                    orderable: false,
                    defaultContent: '<a class="btn btn-theme btn-sm rounded edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil" style="color:white;"></i></a>' +
                    '<a data-toggle="tooltip" data-placement="top" data-original-title="Delete"><button class="btn btn-danger btn-sm rounded delete" data-toggle="modal" data-target="#delete"><i class="fa fa-times"></i></button></a>',
                    targets: 4
                },
                {
                    className: "dt-center",
                    targets: [0, 3, 4]
                },
                {
                    width: "5%",
                    targets: 0,
                },
            ],
        });

        $(document).on("click", "#user-list a button.delete", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = userDatatable.fnGetPosition(target_row);
            }
            var username = userDatatable.fnGetData(position)[1];

            $("#delete form").attr("action", baseUrl + "users/delete?username=" + username);
        });

        $(document).on("click", "#user-list a.edit", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = userDatatable.fnGetPosition(target_row);
            }
            var username = userDatatable.fnGetData(position)[1];

            window.open(baseUrl + "users/edit?id=" + username, "_self");
        });
    }

    if ($("#question-list").length) {
        var url = window.location.href.split("/");
        var survey_id = url[url.length-1];

        var questionDatatable = $("#question-list").dataTable({
            autoWidth: false,
            responsive: true,
            ajax: baseUrl + 'question/ajax?id='+ survey_id,
            columnDefs: [
                {
                    orderable: false,
                    defaultContent: '<a class="btn btn-theme btn-sm rounded edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil" style="color:white;"></i></a>' +
                    '<a data-toggle="tooltip" data-placement="top" data-original-title="Delete"><button class="btn btn-danger btn-sm rounded delete" data-toggle="modal" data-target="#delete"><i class="fa fa-times"></i></button></a>',
                    targets: 5
                },
                {
                    className: "dt-center",
                    targets: [1, 3, 4, 5]
                },
                {
                    width: "5%",
                    targets: 1
                },
                {
                    visible: false,
                    targets: 0
                }
            ],
        });

        $(document).on("click", "#question-list a button.delete", function (e) {
            e.preventDefault();

            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = questionDatatable.fnGetPosition(target_row);
            }
            var id = questionDatatable.fnGetData(position)[0];

            $("#delete form").attr("action", baseUrl + "question/delete?id=" + id);
        });

        $(document).on("click", "#question-list a.edit", function (e) {
            $("#edit").modal('show');

            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = questionDatatable.fnGetPosition(target_row);
            }
            var qst_id = questionDatatable.fnGetData(position)[0];
            var question = questionDatatable.fnGetData(position)[2];

            $("#edit #qst_id").val(qst_id);
            $("#edit #question").val(question);
            $("#edit form").attr("action", baseUrl + "question/edit");
        });
    }

    $("input:radio[name=is_subject]").click(function () {
        if(this.value=='0'){
            $('#sample').fadeIn('slow');
            $('#sample').show();
            $("input[name='sample[]']").attr("required", "required");
        }else if(this.value=='1'){
            $("#mhs").prop('checked', false);
            $("#dsn").prop('checked', false);
            $("#pgw").prop('checked', false);
            $('#sample').fadeOut('slow');
            $("input[name='sample[]']").attr("required",false);
            $('#sample').hide();
        }
    });

    $("input:checkbox").click(function () {
        var s = $("input:checkbox:checked").length;
        if(s>0){
            $("input[name='sample[]']").attr("required",false);
        }else if(s==0){
            $("input[name='sample[]']").attr("required", "required");
        }
    });

    if ($("input[name=upd_mode]").val() == 'display') {
        $("#pustaha-container input:visible, #pustaha-container select:visible, #pustaha-container textarea:visible").attr("disabled", true);
    }

    if ($(".select2").length) {
        $(".select2").select2();
    }

    $(document).on('change','#answer_types',function () {
        var value = $('option:selected', this).val();
        if(value==1){
            var p = '<div class="col-md-2">Jumlah pilihan </div>'
                +'<div class="col-md-10">'
                +'<input type="number" name="answer_total"'
                +'class="form-control" placeholder="input nilai jenis jawaban" id="total_choices"'
                +'required/>'
                +'<span class="alert alert-danger col-md-4 col-md-offset-4" style="display: none !important;">'
                +'</span></div> ';

            $("#jumlah_skala_ans").html(p).fadeIn('slow');

        }else if(value==2){
            var p = '<div class="col-md-2">Jumlah pilihan </div>'
                +'<div class="col-md-10">'
                +'<input type="number" name="total_choices"'
                +'class="form-control" placeholder="input nilai jenis jawaban" id="total_choices"'
                +'required/>'
                +'<span class="alert alert-danger col-md-4 col-md-offset-4" style="display: none !important;">'
                +'</span></div> ';

            $("#jumlah_skala_ans").html(p).fadeIn('slow');

        }else if(value==3 || value==4 || value==5){
            $('#jumlah_skala_ans').fadeOut('slow').empty();
            $('#value_ans').fadeOut('slow').empty();
            // $("#jumlah_skala_ans").;
            // $("#value_ans").fadeOut();
        }
    });

    $("#jumlah_skala_ans").bind("keyup change","#total_choices", function(){
        var val = $('#total_choices').val();
        var html = " ";

        if(!$.isNumeric(val)){
            $("#value_ans").html("");
            notify('Jumlah pilihan harus berupa angka','danger');
        }else if(val<0){
            $("#value_ans").html("");
            notify('Jumlah pilihan harus lebih dari 0','danger');
        }
        else if(val==0){
            $("#value_ans").html("");
            notify('Jumlah pilihan tidak boleh sama dengan 0','danger');
        }else if(val==1){
            $("#value_ans").html("");
            notify('Jumlah pilihan harus lebih dari 1','danger');
        }else if(val>30){
            $("#value_ans").html("");
            notify('Jumlah pilihan tidak boleh lebih dari 30','danger');
        }else{
            html += "<div class='col-md-2'>Nilai Pilihan Jawaban</div><div class='col-md-10'>";
            for(i=0 ; i<val ; i++){
                html += "<input type='text' name='choices[]' class='form-control' placeholder='Input nilai pilihan jawaban' required/><br/>";
            }
            html+="</div>";
            $("#value_ans").fadeIn('slow').html(html);
        }
    });

    $('#save_add').click(function(e) {
        var myForm = $('#form_addQst');
        if (!myForm[0].checkValidity()) {
            myForm.find(':submit').click();
        }else{
            e.preventDefault();
            $("#save_add").val('Process . . .');
            $.ajax({
                url:baseUrl + 'question/create',
                type:'POST',
                data:$('#form_addQst').serialize(),
                success:function(result){
                    $("#save_add").val('Simpan dan Tambah');
                    $('html, body').animate({ scrollTop: 0 }, 300);
                    if(result=='success'){
                        notify('Pertanyaan berhasil ditambah','success');
                        $('#tambah_qst').fadeOut('slow');
                        $('#tambah_qst').fadeIn('slow').delay(2000);
                        $('#jumlah_skala_ans').empty();
                        $('#value_ans').empty();
                        myForm.trigger("reset");

                        $.post(baseUrl + 'question/getQstTotal',
                            {'_token': $('meta[name=csrf-token]').attr('content'),
                                survey_id:$("#survey_id").val()
                            },
                            function(html){
                                $("#jlh_tanya").html(html);
                            }
                        );
                    }else{
                        notify('Pertanyaan gagal ditambah, silahkan periksa kembali','danger');
                    }
                    // console.log(result);
                }
            });
        }
    });

    $('#save_list').click(function(e) {
        var myForm = $('#form_addQst');
        var survey_id = $("#survey_id").val()
        if (!myForm[0].checkValidity()) {
            myForm.find(':submit').click();
        }else{
            e.preventDefault();
            $("#save_add").val('Process . . .');
            $.ajax({
                url:baseUrl + 'question/create',
                type:'POST',
                data:$('#form_addQst').serialize(),
                success:function(result){
                    $("#save_add").val('Simpan dan Lihat Daftar');
                    $('html, body').animate({ scrollTop: 0 }, 300);
                    if(result=='success'){
                        notify('Pertanyaan berhasil ditambah','success');
                        setTimeout(function(){ window.open(baseUrl + "question/show?id=" + survey_id, "_self"); }, 800);
                    }else{
                        notify('Pertanyaan gagal ditambah, silahkan periksa kembali','danger');
                    }
                    console.log(result);
                }
            });
        }
    });

    $('#form_question').on('submit', function (e) {
        e.preventDefault();
        $("#survey-submit").val('Process . . .');
        $.ajax({
            url: baseUrl + 'survey/answer',
            type:'POST',
            data:$('#form_question').serialize(),
            success:function(result){
                $('html, body').animate({ scrollTop: 0 }, 300);
                $("#survey-submit").val('Submit');

                console.log(result);
                if(result=="success"){
                    $('html, body').animate({ scrollTop: 0 }, 300);
                    notify('Survey berhasil dijawab','success');
                    setTimeout(function(){ window.open(baseUrl + "/survey", "_self"); }, 800);
                }else{
                    notify('Survey gaggal dijawab, silahkan periksa kembali jawaban anda','danger');
                }
            }
        });
    });

    $("#data").DataTable();

    $('#back-top').on('click', function (e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 700);
    });

    var i = 0;
    while (i < 5) {
        var element = $("#datepicker");
        if (i > 0) {
            element = $("#datepicker" + i);
        }
        if (element.length) {
            element.datepicker({
                changeMonth: true,
                changeYear: true
            });
        }
        i++;
    }

    var autocomp_opt = {
        source: function (request, response) {
            $.ajax({
                url: baseUrl + '/users/ajax/search',
                dataType: "json",
                data: {
                    query: request.term,
                    limit: 10
                },
                success: function (data) {
                    var transformed = $.map(data, function (el) {
                        return {
                            label: el.label,
                            id: el.username,
                            full_name: el.full_name
                        };
                    });
                    response(transformed);
                }
            });
        },
        select: function (event, ui) {
            $("input[name=full_name]").val(ui.item.full_name);
            $("input[name=username]").val(ui.item.id);
            $(this).parents("tr").find("input[name^=item_username]").val(ui.item.id);
            $('.search-employee').trigger('change');
        }
    };

    if ($(".search-employee").length) {
        $(".search-employee:enabled").autocomplete(autocomp_opt);
    }

    $('.table-add').click(function (e) {
        e.preventDefault();
        if($("#item-table").length){
            var v_table = $(this).parents(".detail-container").find(".item-table");
        }else if($("#user-auth-table").length){
            var v_table = $("#user-auth-table");
        }
        var $clone = v_table.find('tr.hide').clone(true).removeClass('hide table-line');
        if($("#item-table").length){
            $clone.find("input[name^=item_external]").attr("disabled", false);
            $clone.find("input[name^=item_username]").attr("disabled", false);
            $clone.find("input[name^=item_name]").attr("disabled", true);
            $clone.find("input[name^=item_affiliation]").attr("disabled", true);
        }else if($("#user-auth-table").length){
            $clone.find("select").attr("disabled", false);
            $clone.find("select").addClass("select2");
        }
        v_table.find('table').append($clone);
        if($("#item-table").length){
            $(".search-employee:enabled").autocomplete(autocomp_opt);
        } else if ($("#user-auth-table").length) {
            $(".select2").select2();
        }
    });

    $('.table-remove').click(function (e) {
        e.preventDefault();
        $(this).parents('tr').detach();
    });

    if ($(".date-picker").length) {
        $(".date-picker").datepicker({
            format: 'dd-mm-yyyy'
        });
    }
});