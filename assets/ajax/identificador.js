var Tbl_Identificador;

$.extend($.fn.dataTableExt.oStdClasses, {
    "sFilterInput": "form-control",
    "sLengthSelect": "form-control"
});

$.extend( $.fn.dataTable.defaults, {
    autoWidth: false,
    responsive: true,
    dom: '<"datatable-header"ifl><"datatable"t>',
    language: {
        search: '_INPUT_',
        lengthMenu: '_MENU_',
        searchPlaceholder: "Escribir para filtrar...",
        paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
        "sProcessing": "Procesando...",
        "sZeroRecords": 'Ningún dato disponible en esta tabla',
        "sEmptyTable": 'Ningún dato disponible en esta tabla',
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sLoadingRecords": "Cargando..."
    },
    drawCallback: function () {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
    },
    preDrawCallback: function() {
        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
    }
});

$(function() {
    "use strict";
    load_Ini_Tbl_Identificador();
});

function load_Ini_Tbl_Identificador(){
    "use strict";
    sga.blockUI.loading_body();
    Tbl_Identificador = $('#Tbl_Identificador').DataTable({
        buttons: {
            buttons: [ ]
        },
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
        lengthMenu: [ [7, 10, 25, 50, -1], [7, 10, 25, 50, "Todos"] ],
        Columns:[
            { "mDataProp": "0"},
            { "mDataProp": "1"},
            { "mDataProp": "2"},
            { "mDataProp": "3"},
            { "mDataProp": "4"},
            { "mDataProp": "5"},
            { "mDataProp": "6"},
            { "mDataProp": "7"},
            { "mDataProp": "8"},
            { "mDataProp": "9"}
        ],
        columnDefs: [
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [0]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [1]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [2]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [3]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [4]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [5]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [6]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [7]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [8]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                targets: [9]
            }
        ],
        ajax:{
            url: '../controller/IdentificadorController.php?action=lista_Identificador_All',
            type : "get",
            dataType : "json",
            error: function(e){
                console.log(e.responseText);
            }
        },
        "paging": false,
        "initComplete": function(settings, json) {
            $('#Tbl_Identificador_info').addClass('float-left');
            $('#Tbl_Identificador_filter').addClass('float-right');
            $('.datatable-footer').remove();
            sga.blockUI.unblock_body();
        },
        "drawCallback": function( settings ) {}
    });
}

$(document).on('submit','#frm_genIdentify',function(e) {
    "use strict";
    e.preventDefault();
    let campos = $(this).serialize();
    let idusertk = $('#idustk').val();
    let data = campos+'&idustk='+idusertk;
    let inputDoc = $('#numberdoc');
    sga.blockUI.loading_body();
    console.log(data);
    $.post('../controller/IdentificadorController.php?action=generar_IdentifyCode_JSON', data, function (response) {
        if (parseInt(response.status) === 1) {
            sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
            sga.table.refreshDatatable('#Tbl_Identificador');
            inputDoc.val("");
        }
        else if (parseInt(response.status) === 0) {
            sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
        }
    },"json").fail(function (e) {
        sga.plugins.toastr('error', 'toast-top-right', 'Al generar código identificador, contáctese con el Administrador del sistema, para generar un reporte del incidente..', 'Error');
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#deleteIdentify',function() {
    "use strict";
    let idDentify = $(this).attr('data-id');
    sga.blockUI.loading_body();
    $.post('../controller/IdentificadorController.php?action=delete_IdentifyCode_JSON', {'id':idDentify}, function (response) {
        if (parseInt(response.status) === 1) {
            sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
            sga.table.refreshDatatable('#Tbl_Identificador');
        }
        else if (parseInt(response.status) === 0) {
            sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
        }
    },"json").fail(function () {
        sga.plugins.toastr('error', 'toast-top-right', 'Al eliminar el código identificador, contáctese con el Administrador del sistema, para generar un reporte del incidente..', 'Error');
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#anularIdentify',function() {
    "use strict";
    let idDentify = $(this).attr('data-id');
    sga.blockUI.loading_body();
    $.post('../controller/IdentificadorController.php?action=anular_IdentifyCode_JSON', {'id':idDentify}, function (response) {
        if (parseInt(response.status) === 1) {
            sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
            sga.table.refreshDatatable('#Tbl_Identificador');
        }
        else if (parseInt(response.status) === 0) {
            sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
        }
    },"json").fail(function () {
        sga.plugins.toastr('error', 'toast-top-right', 'Al anular el código identificador, contáctese con el Administrador del sistema, para generar un reporte del incidente..', 'Error');
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

