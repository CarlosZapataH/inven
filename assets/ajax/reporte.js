var TblHistorial,Tbl_Reporte;

$.extend($.fn.dataTableExt.oStdClasses, {
    "sFilterInput": "form-control",
    "sLengthSelect": "form-control"
});

$.extend( $.fn.dataTable.defaults, {
    autoWidth: false,
    responsive: true,
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
    sga.plugins.flatpickr_all('.inputFecha');
    sga.plugins.formatter_date('.inputFecha','/');
    sga.plugins.select2_search('.selectSearch');
    sga.plugins.select2('.selectIClass');
    load_ini_Tbl_Default();
});

$(document).on('click','#chkRango', function(){
    "use strict";
    let inputFecha = $('.inputFecha');
    inputFecha.prop('disabled',true);
    inputFecha.val("");
    if( $(this).is(':checked') ) {
        inputFecha.prop('disabled',false);
    }
});

$(document).on('change','#fDesde_his', function(){
    "use strict";
    validafechas('fDesde_his','fHasta_his');});

$(document).on('change','#fHasta_his', function(){
    "use strict";
    validafechas('fDesde_his','fHasta_his');
});

var table =
    '<div class="page-title text-center py-10">\n' +
    '   <p class="breadcrumb-item text-muted mb-0">Muestra las salidas de los materiales generados al colaborador.</p>\n' +
    '</div>\n' +
    '<div class="card card-shadow">\n' +
    '   <div class="table-responsive">\n' +
    '       <table id="Tbl_Despacho" class="table datatable-responsive-row-control">\n' +
    '           <thead>\n' +
    '               <tr>\n' +
    '                   <th class="text-center">Servicio</th>\n' +
    '                   <th class="text-center">Almacen</th>\n' +
    '                   <th class="text-center">Nro. Documento</th>\n' +
    '                   <th class="text-center">Colaborador</th>\n' +
    '                   <th class="text-center">Fecha entrega</th>\n' +
    '                   <th class="text-center">Nro. Registro</th>\n' +
    '                   <th class="text-center">Tipo Validación</th>\n' +
    '                   <th class="text-center">Estado</th>\n' +
    '                   <th class="text-center" style="width:50px"></th>\n' +
    '               </tr>\n' +
    '           </thead>' +
    '           <tbody></tbody>\n' +
    '       </table>\n' +
    '   </div>\n' +
    '</div>';

$(document).on('submit','#formDespacho', function(e){
    "use strict";
    e.preventDefault();
    let idNdoc = $('#ndoc_his');
    let divResponse = $('#divResponse');
    let data = $(this).serialize();
    divResponse.empty();
    console.log(data);
    let NRegistros = 0;
    let idColaborador = 0;
    if(idNdoc.val().length >=8 && idNdoc.val().length <=12) {
        sga.blockUI.loading_body();
        $.post('../controller/MaterialController.php?action=lista_Despachos_Reporte_xColaborador_JSON', data,
            function (response) {
                if (parseInt(response.status) === 1) {
                    divResponse.append(table);
                    console.log(response);
                    let tbody = $('#Tbl_Despacho > tbody');
                    NRegistros = response.data.length;
                    idColaborador = response.identify;
                    for(let i = 0; i < response.data.length; i++){
                        let btnAnula = "";
                        if(parseInt(response.data[i][11]) === 1) {
                            btnAnula ='<a class="text-danger-800 cursor-pointer btn-hover-transform ml-10" title="Anular despacho" data-id="' + response.data[i][8] + '" id="btnAnulaDespacho">' +
                                      ' <i class="ti-na fz-22"></i>' +
                                      '</a>';
                        }
                        let row =
                            '<tr>\n' +
                            '   <td class="text-center align-middle">'+response.data[i][0]+'</td>\n' +
                            '   <td class="text-center align-middle">'+response.data[i][1]+'</td>\n' +
                            '   <td class="text-center align-middle">'+response.data[i][2]+'</td>\n' +
                            '   <td class="text-center align-middle">'+response.data[i][3]+'</td>\n' +
                            '   <td class="text-center align-middle">'+response.data[i][4]+'</td>\n' +
                            '   <td class="text-center align-middle">'+response.data[i][5]+'</td>\n' +
                            '   <td class="text-center align-middle">'+response.data[i][6]+'</td>\n' +
                            '   <td class="text-center align-middle '+response.data[i][10]+'">'+response.data[i][7]+'</td>\n' +
                            '   <td class="text-center align-middle">' +
                            '       <a class="text-muted cursor-pointer text-hover-primary btn-hover-transform" title="Descargar despacho" id="btnGeneratePDF" data-id="'+response.data[i][8]+'" data-option="0"><i class="fa fa-file-pdf-o fz-22 text-danger-600"></i></a>'+
                                    btnAnula+
                            '   </td>\n' +
                            '</tr>';
                        tbody.append(row);
                    }
                }
                else if (parseInt(response.status) === 0) {
                    idNdoc.val("");
                    idNdoc.focus();
                    sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error al realizar búsqueda');
                }
            },"json").always(function () {
            sga.blockUI.unblock_body();
            TblHistorial = $('#Tbl_Despacho').DataTable({
                dom: '<"datatable-header"ifB><"datatable"t>',
                buttons: [],
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                },
                "paging": false,
                "initComplete": function(settings, json) {
                    $('#Tbl_Despacho_info').addClass('float-left ml-15');
                    $('#Tbl_Despacho_filter').addClass('float-right mr-15');
                    console.log(NRegistros);
                    if(NRegistros > 0) {
                        let dtButtons = $('#Tbl_Despacho_wrapper > div.datatable-header > div.dt-buttons');
                        let page = '../../app/reporte-Consolidado-Export.php?'+data+'&idcol='+idColaborador;
                        console.log(page);
                        let btnExport = '<a class="btn btn-outline-secondary btn-sm btn-sm-export cursor-pointer" href="'+page+'"><span class="fa fa-file-excel-o position-left"></span>Consolidado</a>';
                        dtButtons.append(btnExport);
                    }
                }
            });
        });
    }
    else{
        idNdoc.val("");
        idNdoc.focus();
        sga.plugins.toastr('warning', 'toast-top-right', 'Debe ingresar un formato válido del número de documento.<br>DNI: 8 dígitos<br>CEX: 12 dígitos', 'Advertencia');
    }
});

$(document).on('click','#btnGeneratePDF',function(){
    "use strict";
    let idDespacho = $(this).attr('data-id');
    let sendmailOPT = $(this).attr('data-option');
    let page = '../app/Reporte-Despacho-PDF.php?idDespacho='+idDespacho+'&optionMail='+sendmailOPT;
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
                sga.plugins.toastr('success', 'toast-top-right', "Formato generado satisfactoriamente.", 'Vale Despacho');
            }, 5000);
        }
    });
});

$(document).on('click','#btnAnulaDespacho',function(){
    "use strict";
    let idDespacho = $(this).attr('data-id');
    let idUsuario = $('#idustk').val();
    let modalLoading = $('#ModalProgressBar_Load');
    sga.wait.modal('ModalProgressBar_Load');
    modalLoading.modal("show");
    let modalDefault = $('#ModalAction_ContainerForm');
    $.get('../controller/MaterialController.php?action=load_campos_AnulaDespacho', {'iddes':idDespacho,'idus':idUsuario}, function (response) {
        modalDefault.html(response);
    }).always(function () {
        modalLoading.modal("hide");
        modalLoading.html("");
        modalLoading.hide();
        modalDefault.modal("show");
    });
});

$(document).on('submit','#formAnulaDespacho', function(e) {
    "use strict";
    e.preventDefault();
    let data =  $(this).serialize();
    let modalDefault = $('#ModalAction_ContainerForm');
    sga.blockUI.loading_body();
    $.post('../controller/MaterialController.php?action=anular_Despacjo_JSON', data, function (response) {
        console.log(response);
        if (parseInt(response.status) === 1) {
            swal({
                text: "Despacho ANULADO satisfactoriamente.",
                type: "success",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Aceptar'
            }).then(function () {
                modalDefault.modal("hide");
                modalDefault.empty();
                //Actualizar tabla
            });
        }
        else if (parseInt(response.status) === 0) {
            sga.error.show('danger', 'mensaje_error_val', response.message);
            window.setTimeout(function () { $('#mensaje_error_val').html(""); }, 6000);
        }
    },"json").fail(function (e) {
        sga.error.show('danger', 'mensaje_error_val', "Se produjo un error al anular el despacho, vuelva a intentarlo, si el problema persiste contactese con el Administrador.");
        window.setTimeout(function () { $('#mensaje_error_val').html("");}, 6000);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});


/************* Consumo ******************/
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

$(document).on('change','#fechaini', function(){
    "use strict";
    validafechas('fechaini','fechafin');
});

function validafechas(idFechaIni,idFechaFin) {
    "use strict";
    let inputFecha = $('#'+idFechaIni);
    let inputFechaNext = $('#'+idFechaFin);
    if(inputFecha.val() !== "") {
        if (sga.funcion.existeFecha(inputFecha.val()) === true) {
            if(inputFechaNext.val() !== "") {
                if(sga.funcion.compara_dateMenorMayor(inputFecha.val(),inputFechaNext.val()) === false){
                    inputFecha.val("");
                    inputFechaNext.val("");
                    inputFecha.focus();
                    sga.plugins.toastr('error', 'toast-top-right', 'la fecha de inicio no puede ser mayor a la de fin', 'Error');
                }
            }
        }
        else{
            inputFecha.val("");
            inputFecha.focus();
            sga.plugins.toastr('warning', 'toast-top-right', 'Debe ingresar una fecha valida', 'Advertencia');
        }
    }
}

$(document).on('change','#fechafin', function(){
    "use strict";
    validafechas('fechaini','fechafin');
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
                targets: [7]
            }
        ],
        "initComplete": function(settings, json) {
            sga.blockUI.unblock_body();
            $('.datatable-header').hide();
            $('#Tbl_Reporte_length').hide();
            $('#Tbl_Reporte_filter').hide();
        }
    });
}

$(document).on('submit', '#formReporte', function(e) {
    "use strict";
    e.preventDefault();
    let IdAlmacen = $('#IdAlmacen');
    let fechaini = $('#fechaini');
    let fechafin = $('#fechafin');
    if(IdAlmacen.val() !== "") {
        let datos = {
            'almacen': IdAlmacen.val(),
            'fechaini': fechaini.val(),
            'fechafin': fechafin.val()
        };
        loadTbl_Despachos_Transacciones(datos);
    }
    else {
        sga.plugins.toastr('error', 'toast-top-right', 'Debe completar los campos Principales para realizar la búsqueda: <b>Almacén</b> y <b>Transacción</b>.', 'Error búsqueda');
    }
});

function loadTbl_Despachos_Transacciones(datos){
    "use strict";
    sga.blockUI.loading_body();
    Tbl_Reporte.destroy();
    Tbl_Reporte = $('#Tbl_Reporte').DataTable({
        dom: '<"datatable-header"flB><"datatable"t><"datatable-footer"ip>',
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
            { "mDataProp": "7"},
            { "mDataProp": "8"}
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
                searchable: true,
                targets: [7]
            },
            {
                className: 'text-center align-middle',
                visible: true,
                searchable: true,
                targets: [8]
            }
        ],
        ajax:{
            url: '../controller/MaterialController.php?action=lista_Consumos_xAlmacen_JSON',
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
            if( parseInt(json.data.length) > 0) {
                let dtButtons = $('#Tbl_Reporte_wrapper > div.datatable-header > div.dt-buttons');
                let page = '../../app/reporte-Consumo-Export.php?almacen='+datos.almacen+'&fechaini='+datos.fechaini+'&fechafin='+datos.fechafin;
                let btnExport = '<a class="btn btn-outline-secondary btn-sm mr-15 btn-sm-export" href="'+page+'"><span class="fa fa-file-excel-o position-left"></span>Exportar</a>';
                dtButtons.append(btnExport);
            }
            $('#Tbl_Reporte_length').addClass('mr-15');
            $('#Tbl_Reporte_filter').addClass('ml-15');
        },
        "drawCallback": function( settings ) {  }
    });
}

