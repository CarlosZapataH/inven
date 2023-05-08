var Tbl_Personal;

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
    sga.plugins.select2_search('.classSelect');

    let IdServicio = $('#IdServicioSearch').val();
    load_Ini_Tbl_Personal_xServicio(IdServicio)
});


/********************************** COLABORADOR ***********************************/
$(document).on('submit','#frm_searchPersonal', function(e){
    "use strict";
    e.preventDefault();
    let idNdoc = $('#ndoc_col');

    let divResponse = $('#divIResponse');
    divResponse.empty()
    if(idNdoc.val().length >=8 && idNdoc.val().length <=12) {
        let data = {
            'ndoc':$('#ndoc_col').val(),
            'edit':$('#acc_edit').val(),
            'del':$('#acc_del').val()
        };
        sga.blockUI.loading_body();
        $.post('../controller/ColaboradorController.php?action=search_Colaborador_IN', data,
            function (response) {
                divResponse.append(response);
            }).always(function () {
            sga.blockUI.unblock_body();
            sga.plugins.select2_search('.selectSearch');
            sga.plugins.formatter_date('.inputFecha', '/');
            sga.plugins.select2_search('.selectSearch');
        });
    }
    else{
        idNdoc.val("");
        idNdoc.focus();
        sga.plugins.toastr('warning', 'toast-top-right', 'Debe ingresar un formato valido del número de documento.<br>DNI: 8 dígitos<br>CEX: 12 dígitos', 'Advertencia');
    }
});

$(document).on('submit','#formNewPersonal', function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    Swal.fire({
        html: 'Se realizará el registro del Personal.<br>Desea continuar...!.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/ColaboradorController.php?action=registrar_datosPersonal_JSON', data, function (response) {
                if (parseInt(response.status) === 1) {
                    sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
                    window.setTimeout(function () {
                        resetIni_Campos();
                    }, 6000);
                }
                else if (parseInt(response.status) === 2) {
                    sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
                    window.setTimeout(function () {
                        resetIni_Campos();
                    }, 6000);
                }
                else if (parseInt(response.status) === 0) {
                    sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
                }
            },"json").fail(function (e) {
                sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar registrar el Personal ingresado, vuelva a intentarlo y si el problema persiste contáctese con el Administrador.', 'Error');
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click', '#btnCancelPer', function(){
    "use strict";
    resetIni_Campos();
});

$(document).on('click','#ImportPersonal',function(){
    "use strict";
    let divResponse = $('#divIResponse');
    divResponse.empty();
    sga.blockUI.loading_body();
    $.get('../controller/ColaboradorController.php?action=loadCampos_importColaborador', function (response) {
        divResponse.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
        divResponse.show();
        sga.plugins.inputFile('#file_datacol','excel');
        sga.plugins.select2_search('.selectSearch');
        sga.funcion.pageTop();
    });
});

$(document).on('click','#btnDelete', function() {
    "use strict";
    let id = $(this).attr('data-id');
    swal.fire({
        html: 'Se va a eliminar el Colaborador seleccionado.<br>Una vez realizada esta acción no podrá ser revertida.<br>Desea continuar..!!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/ColaboradorController.php?action=delete_Colaborador_JSON', {'id':id}, function (response) {
                if (parseInt(response.status) === 1) {
                    sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
                    resetIni_Campos();
                }
                else if (parseInt(response.status) === 0) {
                    sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
                }
            },"json").fail(function (e) {
                sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar Eliminar al Colaborador seleccionado, vuelva a intentarlo y si el problema persiste contáctese con el Administrador.', 'Error');
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click','#btnActivatedEdit', function(e) {
    "use strict";
    e.preventDefault();
    let btnActiva = $(this);
    btnActiva.hide();
    let btnDelete = $('#btnDelete');
    btnDelete.hide();
    let classEnabled = $('.classEnabled');
    classEnabled.prop('disabled',false);
    let divActions = $('#divActions');
    divActions.empty();
    let btnAction =
        '<button type="button" id="btnCancelEditActv" class="btn btn-light btn-lg mr-20">\n' +
        '   <i class="ti-close position-left"></i>\n' +
        '   Cancelar\n' +
        '</button>'+
        '<button type="submit" class="btn btn-warning btn-hover-transform btn-lg">\n' +
        '   <i class="ti-save position-left"></i>\n' +
        '   Actualizar\n' +
        '</button>\n';
    divActions.append(btnAction);
    sga.plugins.select2_search('.selectSearch');
});

$(document).on('click','#btnCancelEditActv', function() {
    "use strict";
    let btnActiva = $('#btnActivatedEdit');
    btnActiva.show();
    let btnDelete = $('#btnDelete');
    btnDelete.show();
    let classEnabled = $('.classEnabled');
    classEnabled.prop('disabled',true);
    let divActions = $('#divActions');
    divActions.empty();
    sga.plugins.select2_search('.selectSearch');
});

$(document).on('submit','#fmrLoad_DataPersonal',function(e){
    "use strict";
    e.preventDefault();
    let btnDisabled = $('.btnDisabledc');
    let mensajeIFile = $('#divMsg_iFilec');
    btnDisabled.prop('disabled',true);
    mensajeIFile.empty();
    let idServicio = $('#idServicio').val();
    let formdata = new FormData($(this)[0]);
    formdata.append('idServicio',idServicio);
    $.ajax({
        url: '../controller/ColaboradorController.php?action=load_File_Personal',
        type: "POST",
        data: formdata,
        dataType: "json",
        cache: false,
        contentType: false, //No especificamos ningún tipo de dato
        enctype: 'multipart/form-data',
        processData:false, //Evitamos que JQuery procese los datos, daría error
        beforeSend: function(){
            sga.plugins.inputFile_disable('#file_datacol');
            sga.wait.append('#divMsg_iFilec');
        },
        success: function (response) {
            if(parseInt(response.status)===0 || parseInt(response.status)===3){
                swal.fire({
                    text: response.message,
                    type: "error",
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Aceptar'
                }).then(function () {
                    sga.plugins.inputFile_clear('#file_datacol');
                    sga.plugins.inputFile_enable('#file_datacol');
                    btnDisabled.prop('disabled',false);
                });
            }
            else if(parseInt(response.status)===1){
                swal.fire({
                    html: '<code>[' + response.succesLoad + ']</code>'+response.message,
                    type: "success",
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Aceptar'
                }).then(function () {
                    resetIni_Campos();
                });
            }
            else if(parseInt(response.status)===2){
                let btngenFileError = "";
                if(response.dataError.length > 0){
                    btngenFileError = '<div><a class="btn btn-outline-success btn-sm btn-hover-transform mt-10" href="../assets/error-file/'+response.file+'" download="Report-Error.xlsx">' +
                        '   <b><i class="icon-download4 position-left"></i></b>\n' +
                        '   Descargar file Error [' + response.dataError.length + ']'+
                        '</a></div>';
                }
                swal.fire({
                    html: '<code>[' + response.succesLoad + ']</code>'+response.message+btngenFileError,
                    type: "warning",
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Aceptar'
                }).then(function () {
                    resetIni_Campos();
                });
            }
        }
    }).fail(function (e) {
        sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar cargar la plantilla, vuelva a intentarlo y si el problema persiste contáctese con el Administrador.', 'Error');
        sga.plugins.inputFile_clear('#file_datacol');
        sga.plugins.inputFile_enable('#file_datacol');
        btnDisabled.prop('disabled',false);
    }).always(function () {
        sga.wait.remove('#divMsg_iFilec');
    });
});

$(document).on('click', '#btn_CancelLista', function(){
    "use strict";
    resetIni_Campos();
});

function resetIni_Campos() {
    "use strict";
    let divMatricula = $('#divMatricula');
    let divTabla = $('#divTabla');
    divMatricula.show();
    divTabla.hide();
    let divResponse = $('#divIResponse');
    let idNumber = $('#ndoc_col');
    idNumber.val("");
    divResponse.empty();
    sga.funcion.pageTop();
    Tbl_Personal.clear().draw();
}

$(document).on('click','#btnCancelCol_Load', function() {
    "use strict";
    let divIResponse = $('#divIResponse');
    divIResponse.empty();
});

/********************************** LISTA COLABORADOR ***********************************/
$(document).on('click','#viewListaPersonal', function() {
    "use strict";
    let divMatricula = $('#divMatricula');
    let divTabla = $('#divTabla');
    let divIResponse = $('#divIResponse');
    divMatricula.hide();
    divTabla.show();
    divIResponse.empty();
});

$(document).on('change', '#IdServicioSearch', function() {
    "use strict";
    let IdServicioUsuario = $(this).val();
    Tbl_Personal.destroy();
    load_Ini_Tbl_Personal_xServicio(IdServicioUsuario);
});

function load_Ini_Tbl_Personal_xServicio(IdServicioUsuario){
    "use strict";
    sga.blockUI.loading_body();
    Tbl_Personal = $('#Tbl_Personal').DataTable({
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
            { "mDataProp": "6"}
        ],
        columnDefs: [
            {
                className: 'text-center align-middle',
                targets: [0]
            },
            {
                className: 'text-center align-middle',
                targets: [1]
            },
            {
                className: 'text-center align-middle',
                targets: [2]
            },
            {
                className: 'text-center align-middle',
                targets: [3]
            },
            {
                className: 'text-center align-middle',
                targets: [4]
            },
            {
                className: 'text-center align-middle',
                targets: [5]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                searchable: false,
                targets: [6]
            }
        ],
        order:[3 , 'asc'],
        ajax:{
            url: '../controller/ColaboradorController.php?action=lista_Colaborador_xServicio_JSON',
            type : "get",
            data : {'IdServicioUsuario':IdServicioUsuario},
            dataType : "json",
            error: function(e){
                console.log(e.responseText);
            }
        },
        "initComplete": function(settings, json) {
            let IdServicioUsuario = $('#IdServicioUsuario');
            if(IdServicioUsuario.val() > 0) {
                let btnAddPersonal = '<button type="button" class="btn btn-info btn-sm mr-10" id="AddPersonal" style="padding: 0.42rem 1.2rem;border-radius: 3px;" title="Agregar"><i class="icon-plus fz-18 position-left"></i>Agregar</button>'+
                    '<button type="button" class="btn btn-warning float-right btn-sm mr-10" id="ImportPersonal" style="padding: 0.42rem 1.2rem;border-radius: 3px;" title="Importar"><i class="icon-cloud-upload fz-18 position-left"></i> Importar </button>';
                $('#Tbl_Personal_wrapper > div.datatable-header > div.dt-buttons').prepend(btnAddPersonal);
            }
            $('#Tbl_Personal_wrapper > div.datatable-header > div.dataTables_filter > label > input').attr('style','margin-left: 15px !important');
            $('#Tbl_Personal_wrapper > div.datatable-header > div.dataTables_length > label > select').addClass('mr-15');
            sga.blockUI.unblock_body();
        },
        "drawCallback": function( settings ) {
        }
    });
}

$(document).on('click','#bajaAltaCol', function() {
    "use strict";
    let id = $(this).attr('data-id');
    let estado = $(this).attr('data-opc');
    sga.blockUI.loading_body();
    $.post('../controller/ColaboradorController.php?action=altaBaja_Colaborador_JSON', {'id':id,'estado':estado}, function (response) {
        if (parseInt(response.status) === 1) {
            sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
            sga.table.refreshDatatable('#Tbl_Personal');
        }
        else if (parseInt(response.status) === 0) {
            sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
        }
    },"json").fail(function (e) {
        sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar actualizar el estado del Colaborador, vuelva a intentarlo y si el problema persiste contactese con el Administrador', 'Error');
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#deleteCol', function() {
    "use strict";
    let id = $(this).attr('data-id');
    swal.fire({
        html: 'Se eliminará al Colaborador seleccionado.<br>Una vez realizada esta acción no podrá ser revertida.<br>Desea continuar..!!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/ColaboradorController.php?action=delete_Colaborador_JSON', {'id':id}, function (response) {
                if (parseInt(response.status) === 1) {
                    sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
                    sga.table.refreshDatatable('#Tbl_Personal');
                }
                else if (parseInt(response.status) === 0) {
                    sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
                }
            },"json").fail(function (e) {
                sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar Eliminar al Colaborador seleccionado, vuelva a intentarlo y si el problema persiste contáctese con el Administrador.', 'Error');
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});