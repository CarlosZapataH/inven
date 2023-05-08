var swal, $Tbl_Perfil;
var $table;
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
    loadTabla_INI_Perfil();
});

$(document).on('click','#btnOptionsGuia_Export',function(){
    "use strict";
    let idTransfer = $(this).attr('data-id');
    swal.close();
    let modalLoading = $('#ModalProgressBar_Load');
    modalLoading.empty();
    sga.wait.modal('ModalProgressBar_Load');
    modalLoading.modal("show");
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.empty();
    $.get('../controller/InventarioController.php?action=loadview_optionGuia', {'id':idTransfer}, function (response) {
        modalDefault.html(response);
    }).always(function () {
        modalLoading.modal("hide");
        modalLoading.html("");
        modalLoading.hide();
        modalDefault.modal("show");
    });
});

$(document).on('click','#btnGenerated_exp',function () {
    "use strict";
    let idTransfer = $('#idtransf').val();
    let optionTransfer = $('input:radio[name=rdbOption_g]:checked').val();
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.modal('hide');
    modalDefault.empty();
    let page = '../app/Export-Guia-PDF-Item.php?idTransfer='+idTransfer+'&option='+optionTransfer;
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
                swal.fire({
                    html: 'Guía generada satisfactoriamente',
                    type: "success",
                    showCancelButton: false,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: 'Aceptar'
                }).then(function () {
                    resetIni_Campos();
                });
            }, 5000);
        }
    });
});

$(document).on('click','#btnCancel_exp',function(){
    "use strict";
    let modalDefault = $('#ModalAction_ContainerForm');
    modalDefault.modal('hide');
    modalDefault.empty();
    resetIni_Campos();
});



function loadTabla_INI_Perfil(){
    "use strict";
    let optNew = $('#acc_nuevo').val();
    let opEdit = $('#acc_edit').val();
    let optDelete = $('#acc_del').val();
    let optEnabled = $('#acc_active').val();
    sga.blockUI.loading_body();
    $Tbl_Perfil = $('#Tbl_Perfil').DataTable({
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
            { "mDataProp": "9"},
            { "mDataProp": "10"},
            { "mDataProp": "11"},
            { "mDataProp": "12"},
            { "mDataProp": "13"}
        ],
        columnDefs: [
            {
                className: 'control',
                orderable: false,
                targets: [0]
            },
            {
                'checkboxes': {
                    'selectRow': true
                },
                orderable: false,
                width: "50px",
                targets: [1]
            },
            {
                className: 'text-left',
                targets: [2]
            },
            {
                className: 'text-center',
                targets: [3]
            },
            {
                className: 'text-center',
                targets: [4]
            },{
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
            },
            {
                className: 'text-center',
                targets: [8]
            },
            {
                className: 'text-center',
                targets: [9]
            },
            {
                className: 'text-center',
                targets: [10]
            },
            {
                className: 'text-center',
                targets: [11]
            },
            {
                className: 'text-center',
                targets: [12]
            },

            {
                orderable: false,
                width: "150px",
                className: 'text-center',
                targets: [13]
            }
        ],
        'select': {
            style:    'os',
        },
        ajax:{
            url: '../controller/PerfilController.php?action=lst_Perfil_All_JSON',
            type : "get",
            dataType : "json",
            error: function(e){
                console.log(e.responseText);
            }
        },
        "initComplete": function(settings, json) {
            sga.blockUI.unblock_body();
            $('.dt-checkboxes-select-all > input').addClass('scale-chk-1-5 cursor-pointer');
            $('.dt-checkboxes').addClass('scale-chk-1-5 cursor-pointer');
            if(parseInt(optNew) ===1 || parseInt(opEdit) ===1 || parseInt(optDelete) ===1 || parseInt(optEnabled) ===1) {
                let actions = '<div class="btn-group">';
                actions += '  <button type="button" class="btn btn-sm btn-outline-secundary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acciones</button>';
                actions += '  <div class="dropdown-menu">';
                if (parseInt(optNew) === 1) {
                    actions += '    <a class="dropdown-item cursor-pointer" id="createItem_Btn"><i class="fa fa-plus mr-10"></i>Nuevo</a>';
                }
                if (parseInt(opEdit) === 1) {
                    actions += '    <a class="dropdown-item cursor-pointer" id="editarItem_Btn"><i class="fa fa-edit mr-10"></i>Editar</a>';
                }
                actions += '    <div class="dropdown-divider"></div>';
                if (parseInt(optEnabled) === 1) {
                    actions += '     <a class="dropdown-item cursor-pointer" id="enabSuspItem_Btn" data-estd="1"><i class="fa fa-play mr-5"></i>Habilitar</a>';
                    actions += '     <a class="dropdown-item cursor-pointer" id="enabSuspItem_Btn" data-estd="0"><i class="fa fa-pause mr-5"></i>Suspender</a>';
                }
                if (parseInt(optDelete) === 1) {
                    actions += '    <div class="dropdown-divider"></div>';
                    actions += '    <a class="dropdown-item cursor-pointer" id="deleteItem_Btn" data-status="4"><i class="fa fa-trash mr-10"></i>Eliminar</a>';
                }
                actions += '  </div>';
                actions += '</div>';

                $('#Tbl_Perfil_wrapper > div.datatable-header > div.dt-buttons').prepend(actions);
                $('#Tbl_Perfil_wrapper > div.datatable-header > div.dt-buttons').addClass('p-r-20');
            }
        },
        "drawCallback": function( settings ) {
            $('.dt-checkboxes-select-all > input').addClass('scale-chk-1-5 cursor-pointer');
            $('.dt-checkboxes').addClass('scale-chk-1-5 cursor-pointer');
        }
    });
}

$(document).on('click','#createItem_Btn',function(){
    "use strict";
    let divTabla = $('#divTabla');
    let divResponse = $('#divResponse');
    sga.blockUI.loading_body();
    divTabla.show();
    divResponse.hide();
    divResponse.empty();
    $.get('../controller/PerfilController.php?action=loadCampos_NuevoPerfil_Ajax', function (response) {
        divResponse.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
        divTabla.hide();
        divResponse.show();
    });
});

$(document).on('submit','#formNewPerfil', function(e){
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    sga.blockUI.loading_body();
    $.post('../controller/PerfilController.php?action=registrar_Perfil_JSON', data,
        function (response) {
            if (parseInt(response.status) === 1) {
                swal({
                    text: "Se realizo el registro satisfactoriamente.",
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
            else if (parseInt(response.status) === 0) {
                sga.error.show('danger', 'mensajes_actions_pf', 'Error al realizar el registro');
                window.setTimeout(function () {$('#mensajes_actions_pf').html("");}, 6000);
            }
    },"json").fail(function (e) {
        sga.error.show('danger', 'mensajes_actions_pf', 'Error al realizar el registro');
        window.setTimeout(function () { $('#mensajes_actions_pf').html("");}, 6000);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

function resetIni_Campos() {
    "use strict";
    let divTabla = $('#divTabla');
    let divResponse = $('#divResponse');
    divTabla.show();
    divResponse.hide();
    divResponse.empty();
    sga.table.refreshDatatable_chk('#Tbl_Perfil');
}

$(document).on('click','#btnCancel',function(){
    "use strict";
    resetIni_Campos();
});

$(document).on('click','#enabSuspItem_Btn', function() {
    "use strict";
    let estado = $(this).attr('data-estd');
    let rows_selected = $Tbl_Perfil.column(1).checkboxes.selected();
    let arrayChk = [];
    $.each(rows_selected, function(index, rowId){
        arrayChk.push(parseInt(rowId));
    });
    if(arrayChk.length > 0 ) {
        update_Estado_Perfil(estado, arrayChk);
    }
    else{
        swal({
            text: "Debe seleccionar al menos un Registro.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('click','#deleteItem_Btn', function(e) {
    "use strict";
    e.preventDefault();
    let rows_selected = $Tbl_Perfil.column(1).checkboxes.selected();
    let arrayChk = [];
    $.each(rows_selected, function(index, rowId){
        arrayChk.push(parseInt(rowId));
    });
    if(arrayChk.length > 0) {
        swal.fire({
            html: 'Se va a eliminar los registros seleccionados.<br>Una vez realizada esta acción no podra ser revertida.<br>Desea continuar..!!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.value) {
                sga.blockUI.loading_body();
                $.post('../controller/PerfilController.php?action=delete_Perfil_JSON', {'id':arrayChk}, function (response) {
                    if (parseInt(response.status) === 1 || parseInt(response.status) === 2) {
                        swal({
                            text: "Registro(s) eliminado(s) satisfactoriamente.",
                            type: "success",
                            showCancelButton: false,
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'OK'
                        }).then(function () {
                            sga.table.refreshDatatable_chk('#Tbl_Perfil');
                        });
                    }
                    else if (parseInt(response.status) === 0) {
                        sga.error.show('danger', 'mensajes_actions_pf', 'Error al eliminar los registros seleccionados.');
                        window.setTimeout(function () { $('#mensajes_actions_pf').html(""); }, 6000);
                    }
                },"json").fail(function (e) {
                    sga.error.show('danger', 'mensajes_actions_pf', "Se produjo un error al intentar eliminar los registros, vuelva a intentarlo, si el problema persiste contactese con el Administrador.");
                    window.setTimeout(function () { $('#mensajes_actions_pf').html("");}, 6000);
                }).always(function () {
                    sga.blockUI.unblock_body();
                });
            }
        });
    }
    else if(arrayChk.length === 0){
        swal.fire({
            text: "Debe seleccionar al menos un registro.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

function update_Estado_Perfil(estd,arrayID){
    "use strict";
    sga.blockUI.loading_body();
    $.post('../controller/PerfilController.php?action=Update_Estado_Perfil_JSON', {'id':arrayID, 'estd':estd}, function(response){
        if(parseInt(response.status)===1 || parseInt(response.status)===2){
            swal({
                type: "success",
                text: "Estado(s) actualizado(s) satisfactoriamente",
                showCancelButton: false,
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'OK'
            }).then(function () {
                sga.table.refreshDatatable_chk('#Tbl_Perfil');
            });
        }
        else if(parseInt(response.status) === 0){
            sga.error.show('danger','mensajes_actions_pf','Error al actualizar estado');
            window.setTimeout(function () { $('#mensajes_actions_pf').html(""); }, 5600);
        }
    },"json").fail(function () {
        sga.error.show('danger','mensajes_actions_pf',"Error al actualizar el estado del Registro, intentelo nuevamente si el problema persiste contactese con el Administrador.");
        window.setTimeout(function () { $('#mensajes_actions_pf').html(""); }, 5600);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
}

$(document).on('click','#editarItem_Btn',function(){
    "use strict";
    let rows_selected = $Tbl_Perfil.column(1).checkboxes.selected();
    let arrayChk = [];
    $.each(rows_selected, function(index, rowId){
        arrayChk.push(parseInt(rowId));
    });
    if(arrayChk.length === 1) {
        let $divTabla = $('#divTabla');
        let $divResponse = $('#divResponse');
        $divTabla.show();
        $divResponse.hide();
        sga.blockUI.loading_body();
        $.get('../controller/PerfilController.php?action=loadCampos_EditarPerfil_Ajax', {'id':arrayChk[0]}, function (response) {
            $divResponse.html(response);
        }).always(function () {
            sga.blockUI.unblock_body();
            $divTabla.hide();
            $divResponse.show();
        });
    }
    else if(arrayChk.length === 0){
        swal.fire({
            text: "Debe seleccionar al menos un Registro.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
    else if(arrayChk.length >1){
        swal.fire({
            text: "Debe seleccionar solo un Registro para proceder a editarlo.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'OK'
        });
    }
});

$(document).on('submit','#formEditPerfil', function(e) {
    "use strict";
    e.preventDefault();
    let $data = $(this).serialize();
    Swal.fire({
        html: 'Se va a modificar las datos ingresados.<br>Desea grabar los cambios...!.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/PerfilController.php?action=update_Perfil_JSON', $data, function(response){
                if(parseInt(response.status)===1){
                    swal({
                        type: "success",
                        text: "Registro actualizado satisfactoriamente",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'OK'
                    }).then(function () {
                        resetIni_Campos();
                    });
                }
                else if(parseInt(response.status)===0){
                    sga.error.show('danger','mensajes_actions_pf','Error al realizar la actualización del registro');
                    window.setTimeout(function () { $('#mensajes_actions_pf').html("");}, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger','mensajes_actions_pf',"Error al realizar la actualización de los datos, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                window.setTimeout(function () { $('#mensajes_actions_pf').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click','#btnDelete_Item',function(){
    "use strict";
    let ids = $(this).parent().parent().attr('data-index');
    $(this).parent().parent().remove();
});

$(document).on('click','#btnLoad_ItemAlmacen',function(){
    "use strict";
    let row = '<tr><td class="text-center  fz-18" style="vertical-align: middle; width: 30px; ">1</td><td class="text-left fz-18" style="vertical-align: middle; ">STOCK</td><td class="text-left fz-18" style="vertical-align: middle; ">FILTRO DE PETROLEO SELLADO(ID-THD)-7/8</td><td class="text-left fz-18" style="vertical-align: middle; "></td><td class="text-left fz-18" style="vertical-align: middle; ">0000-00-00</td><td class="text-left fz-18" style="vertical-align: middle; width: 150px; "><input type="text" class="form-control text-center fz-18 font-weight-bold actionCalculoCantidad" id="txt_cant0" value="1" data-pos="0"></td><td class="font-weight-bold fz-18 text-primary-400 text-center" style="vertical-align: middle; width: 80px; "><input type="hidden" id="txt_stk0" value="68">168</td><td class="text-center" style="vertical-align: middle; width: 50px; "><button type="button" class="btn btn-danger btn-hover-transform" data-pos="0" title="Eliminar ítem" id="btnDelete_Item"><i class="fa fa-minus" aria-hidden="true"></i></button></td></tr>'
    $('#tableExample tbody').append(row);
    $table.bootstrapTable('refresh');
});


