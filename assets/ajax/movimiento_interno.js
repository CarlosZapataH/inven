var Tbl_Reporte;
$.extend($.fn.dataTableExt.oStdClasses, {
    "sFilterInput": "form-control",
    "sLengthSelect": "form-control"
});

$.extend( $.fn.dataTable.defaults, {
    autoWidth: false,
    responsive: true,
    dom: '<"datatable-header"flB><"datatable"t><"datatable-footer"ip>',
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
    sga.plugins.select2_search('.selectSearch');
    sga.plugins.selectpicker('.selectMultiple');
    sga.plugins.select2('.selectIClass');
    sga.plugins.flatpickr_rangeInput('#fecha');
    sga.plugins.formatter_daterange('#fecha');
    $('.dropdown-toggle').addClass('bnclass_Selectpiker');
    load_ini_Tbl_Default();
});

function load_ini_Tbl_Default(){
    "use strict";
    sga.blockUI.loading_body();
    Tbl_Reporte = $('#Tbl_Reporte').DataTable({
        buttons: {
            buttons: [ ]
        },
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
        Columns:[
            { "mDataProp": "0"},
            { "mDataProp": "1"},
            { "mDataProp": "2"},
            { "mDataProp": "3"},
            { "mDataProp": "4"},
            { "mDataProp": "5"},
            { "mDataProp": "6"},
            { "mDataProp": "7"}
        ],
        columnDefs: [
            {
                className: 'text-center',
                targets: [0]
            },
            {
                className: 'text-center',
                targets: [1]
            },
            {
                className: 'text-center',
                targets: [2]
            },
            {
                className: 'text-center',
                targets: [3]
            },
            {
                className: 'text-center',
                targets: [4]
            },
            {
                className: 'text-center',
                targets: [5]
            },
            {
                className: 'text-center',
                targets: [6]
            },
            {
                className: 'text-center',
                searchable: false,
                width: "80px",
                targets: [7]
            }
        ],
        "initComplete": function(settings, json) {
            sga.blockUI.unblock_body();
        }
    });
    $('.datatable-header').hide();
    $('#Tbl_Reporte_length').hide();
    $('#Tbl_Reporte_filter').hide();
}

$(document).on('change','#IdServicioUsuario',function(){
    "use strict";
    let id = $(this).val();
    load_Almacenes_xServicioUsuario(id);
});

function load_Almacenes_xServicioUsuario(id) {
    "use strict";
    let $selectID = $('#IdAlmacen');
    $selectID.empty();
    $selectID.prop('disabled', true);
    if(id !== "") {
        $.get('../controller/AlmacenController.php?action=loadSelect_Almacen_ServicioUsuario_JSON', {'idsu': id}, function (lista) {
            $selectID.empty();
            $selectID.append('<option></option>');
            if (lista !== null && parseInt(lista.length) > 0) {
                for (let i = 0; i < parseInt(lista.length); i++) {
                    let $option = $("<option></option>");
                    $option.val(lista[i].id);
                    $option.text(lista[i].texto);
                    $selectID.append($option);
                }
            }
        }, "json").always(function () {
            $selectID.prop('disabled', false);
            sga.plugins.select2('.selectClass');
        });
    }
}

$(document).on('click', '#btnAction_Search', function() {
    "use strict";
    let IdAlmacen = $('#IdAlmacen');
    let transac = $('#transacciones');
    let fecha = $('#fecha');
    let optionReport = $('#acc_report').val();
    if(IdAlmacen.val() !== "" && transac.val() !== null) {
        let datos = {
            'almacen': IdAlmacen.val(),
            'transac': transac.val(),
            'fecha': fecha.val(),
            'optionReport': optionReport
        };

        loadTbl_Inventario_Transacciones(datos);
    }
    else {
        sga.error.show('danger', 'mensajes_actions_rpte', 'Debe completar los campos Principales para realizar la búsqueda: <b>Almacén</b> y <b>Transacción</b>.');
        window.setTimeout(function () { $('#mensajes_actions_rpte').html("");}, 6000);
    }
});

$(document).on('click','#dataExportPDF_Vale',function () {
    "use strict";
    let idmov = $(this).attr('data-id');
    let page = '../app/Export-Vale-PDF-Item.php?idMovimiento='+idmov;
    $.ajax({
        url: page,
        type: 'POST',
        beforeSend: function() {
            sga.blockUI.loading_body();
        },
        success: function(){
            window.location = page;// you can use window.open also
        },
        error: function(xhr) { // if error occured
            console.log("Error occured.please try again");
            console.log(xhr.statusText + " - " +xhr.responseText);
        },
        complete: function() {
            window.setTimeout(function () {
                sga.blockUI.unblock_body();
            }, 5000);
        }
    });
});

function loadTbl_Inventario_Transacciones(datos){
    "use strict";
    let optionImportExport = $('#acc_importExport').val();
    sga.blockUI.loading_body();
    Tbl_Reporte.destroy();
    Tbl_Reporte = $('#Tbl_Reporte').DataTable({
        buttons: {
            buttons: [ ]
        },
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
        lengthMenu: [ [25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"] ],
        Columns:[
            { "mDataProp": "0"},
            { "mDataProp": "1"},
            { "mDataProp": "2"},
            { "mDataProp": "3"},
            { "mDataProp": "4"},
            { "mDataProp": "5"},
            { "mDataProp": "6"},
            { "mDataProp": "7"}
        ],
        columnDefs: [
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [0]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [1]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [2]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [3]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [4]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [5]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [6]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: false,
                width: "80px",
                targets: [7]
            }
        ],
        ajax:{
            url: '../controller/InventarioController.php?action=lst_Inventario_Movimiento_xTransaccion_INT_JSON',
            type : "get",
            data : datos,
            dataType : "json",
            error: function(e){
                console.log(e.responseText);
            },complete:function (datos){
                console.log(datos);
            }
        },
        "initComplete": function(settings, json) {
            sga.blockUI.unblock_body();
            let dtBbuttons = $('#Tbl_Reporte_wrapper > div.datatable-header > div.dt-buttons');
            dtBbuttons.empty();
            if(parseInt(optionImportExport) === 1 && parseInt(json.data.length) > 0) {
                let page = '../../app/movimientos-Export-Interno.php?almacen='+datos.almacen+'&transac='+datos.transac+'&fecha='+datos.fecha;
                let btnExport = '<a class="btn btn-success btn-hover-transform cursor-pointer mr-10 btn-sm" href="'+page+'"><i class="fa fa-file-excel-o mr-10 fz-18"></i>Exportar</a>';
                dtBbuttons.prepend(btnExport);
            }
            $('#Tbl_Reporte_length').addClass('mr-15')
            $('#Tbl_Reporte_filter').addClass('ml-15')
        },
        "drawCallback": function( settings ) {  }
    });
}