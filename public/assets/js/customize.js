/**
 * Created by Yeni on 04/08/2017.
 */
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
                    defaultContent: '<a data-toggle="tooltip" data-placement="top" title="Lihat Pertanyaan"><button class="btn btn-theme btn-sm btn-slideright rounded display"><i class="fa fa-eye" style="color:white;"></i></button></a>' +
                    '<a data-toggle="tooltip" data-placement="top" title="Tambah Pertanyaan"><button class="btn btn-primary btn-sm btn-slideright rounded add"><i class="fa fa-plus" style="color:white;"></i></button></a>',
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

            window.open(baseUrl + "survey/show?id=" + id, "_self");
        });

        $(document).on("click", "#survey-list-admin a button.display", function (e) {
            e.preventDefault();
            var dt_row = $(this).closest("li").data("dt-row");

            if (dt_row >= 0) {
                var position = dt_row;
            } else {
                var target_row = $(this).closest("tr").get(0);
                var position = adminDatatable.fnGetPosition(target_row);
            }
            var id = adminDatatable.fnGetData(position)[0];

            window.open(baseUrl + "pustahas/display?id=" + id, "_self");
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
                    defaultContent: '<a class="btn btn-theme btn-sm rounded answer" data-toggle="tooltip" data-placement="top" title="Jawab Survey"><i class="fa fa-eye" style="color:white;"></i></a>',
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

            window.open(baseUrl + "survey/answer?id=" + id, "_self");
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

    // $('#pilihan_tujuan').change(function () {
    //     alert($('#pilihan_tujuan').val());
    // })

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