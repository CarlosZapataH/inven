var swal, $Tbl_Almacen;

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
    let count_servicio = $('#count_servicio').val();
    if(parseInt(count_servicio)===1){
        let IdServicio = $('#IdServicio').val();
        loadTbl_Almacen_xServicio_INI(IdServicio);
    }
    else {
        load_ini_Tbl_Default();
        sga.plugins.select2_search('.selectedClass');
    }
});

function load_ini_Tbl_Default() {
    "use strict";
    sga.blockUI.loading_body();
    $Tbl_Almacen = $('#Tbl_Almacen').DataTable({
        buttons: {
            buttons: []
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
            { "mDataProp": "5"}
        ],
        columnDefs: [
            {
                className: 'control no-padding',
                orderable: false,
                targets: [0]
            },
            {
                orderable: false,
                width: "50px",
                targets: [1]
            },
            {
                className: 'text-left',
                targets: [2]
            },
            {
                className: 'text-left',
                targets: [3]
            },
            {
                className: 'text-left',
                targets: [4]
            },
            {
                className: 'text-center',
                targets: [5]
            }
        ],
        "initComplete": function(settings, json) {
            sga.blockUI.unblock_body();
            $('#Tbl_Almacen_length').hide();
        },
    });
}

function loadTbl_Almacen_xServicio_INI(IdServicio) {
    "use strict";
    let optNew = $('#acc_nuevo').val();
    let opEdit = $('#acc_edit').val();
    let optDelete = $('#acc_del').val();
    let optEnabled = $('#acc_active').val();
    $Tbl_Almacen = $('#Tbl_Almacen').DataTable({
        buttons: {
            buttons: []
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
            { "mDataProp": "5"}
        ],
        columnDefs: [
            {
                className: 'control no-padding',
                orderable: false,
                targets: [0]
            },
            {
                'checkboxes': {
                    'selectRow': false
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
                className: 'text-left',
                targets: [3]
            },
            {
                className: 'text-center',
                targets: [4]
            },
            {
                orderable: false,
                className: 'text-center',
                targets: [5],
                width: "150px",
            }
        ],
        lengthMenu: [ [7, 10, 25, 50, -1], [7, 10, 25, 50, "Todos"] ],
        order: [2, 'asc'],
        'select': {
            style:    'os',
        },
        ajax:{
            url: '../controller/AlmacenController.php?action=lst_Almacen_xServicio_All_JSON',
            type : "get",
            data : {'idserv':IdServicio},
            dataType : "json",
            error: function(e){
                sga.error.show('danger', 'mensajes_actions_alm', e.responseText);
                window.setTimeout(function () {$('#mensajes_actions_alm').html("");}, 6000);
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
                if(parseInt(optNew) ===1) {
                    actions += '    <a class="dropdown-item cursor-pointer" id="createItem_Btn"><i class="fa fa-plus mr-10"></i>Nuevo</a>';
                }
                if(parseInt(opEdit) === 1) {
                    actions += '    <a class="dropdown-item cursor-pointer" id="editarItem_Btn"><i class="fa fa-edit mr-10"></i>Editar</a>';
                }
                actions += '    <div class="dropdown-divider"></div>';
                if(parseInt(optEnabled) ===1) {
                    actions += '     <a class="dropdown-item cursor-pointer" id="enabSuspItem_Btn" data-estd="1"><i class="fa fa-play mr-5"></i>Habilitar</a>';
                    actions += '     <a class="dropdown-item cursor-pointer" id="enabSuspItem_Btn" data-estd="0"><i class="fa fa-pause mr-5"></i>Suspender</a>';
                }
                if(parseInt(optDelete) === 1) {
                    actions += '    <div class="dropdown-divider"></div>';
                    actions += '    <a class="dropdown-item cursor-pointer" id="deleteItem_Btn" data-status="4"><i class="fa fa-trash mr-10"></i>Eliminar</a>';
                }
                actions += '  </div>';
                actions += '</div>';

                $('#Tbl_Almacen_wrapper > div.datatable-header > div.dt-buttons').prepend(actions);
                $('#Tbl_Almacen_wrapper > div.datatable-header > div.dt-buttons').addClass('p-r-20');
            }
        },
        "drawCallback": function( settings ) {
            $('.dt-checkboxes-select-all > input').addClass('scale-chk-1-5 cursor-pointer');
            $('.dt-checkboxes').addClass('scale-chk-1-5 cursor-pointer');
        }
    });
}

function update_Estado_Almacen(estd,arrayID){
    "use strict";
    sga.blockUI.loading_body();
    $.post('../controller/AlmacenController.php?action=Update_Estado_Almacen_JSON', {'id':arrayID, 'estd':estd}, function(response){
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
                sga.table.refreshDatatable_chk('#Tbl_Almacen');
            });
        }
        else if(parseInt(response.status) === 0){
            sga.error.show('danger','mensajes_actions_alm','Error al actualizar estado');
            window.setTimeout(function () { $('#mensajes_actions_alm').html(""); }, 5600);
        }
    },"json").fail(function () {
        sga.error.show('danger','mensajes_actions_alm',"Error al actualizar el estado del Registro, intentelo nuevamente si el problema persiste contactese con el Administrador.");
        window.setTimeout(function () { $('#mensajes_actions_alm').html(""); }, 5600);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
}

function resetIni_Campos() {
    "use strict";
    let divTabla = $('#divTabla');
    let divResponse = $('#divResponse');
    divTabla.show();
    divResponse.hide();
    divResponse.empty();
    sga.table.refreshDatatable_chk('#Tbl_Almacen');
}

$(document).on('change', '#IdServicio', function() {
    "use strict";
    let IdServicio = $(this).val();
    $Tbl_Almacen.destroy();
    if(IdServicio !== ""){
        loadTbl_Almacen_xServicio_INI(IdServicio);
    }
    else{
        load_ini_Tbl_Default();
        $Tbl_Almacen.clear().draw();
    }
});

$(document).on('click','#enabSuspItem_Btn', function() {
    "use strict";
    let estado = $(this).attr('data-estd');
    let rows_selected = $Tbl_Almacen.column(1).checkboxes.selected();
    let arrayChk = [];
    $.each(rows_selected, function(index, rowId){
        arrayChk.push(parseInt(rowId));
    });
    if(arrayChk.length > 0 ) {
        update_Estado_Almacen(estado, arrayChk);
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
    let rows_selected = $Tbl_Almacen.column(1).checkboxes.selected();
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
                $.post('../controller/AlmacenController.php?action=delete_Almacen_JSON', {'id':arrayChk}, function (response) {
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
                            sga.table.refreshDatatable_chk('#Tbl_Almacen');
                        });
                    }
                    else if (parseInt(response.status) === 0) {
                        sga.error.show('danger', 'mensajes_actions_alm', 'Error al eliminar los registros seleccionados.');
                        window.setTimeout(function () { $('#mensajes_actions_alm').html(""); }, 6000);
                    }
                },"json").fail(function (e) {
                    sga.error.show('danger', 'mensajes_actions_alm', "Se produjo un error al intentar eliminar los registros, vuelva a intentarlo, si el problema persiste contactese con el Administrador.");
                    window.setTimeout(function () { $('#mensajes_actions_alm').html("");}, 6000);
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

$(document).on('click','#createItem_Btn',function(){
    "use strict";
    let idustk = $('#idustk').val();
    let IdServicio = $('#IdServicio').val();
    let divTabla = $('#divTabla');
    let divResponse = $('#divResponse');
    divTabla.show();
    divResponse.hide();
    sga.blockUI.loading_body();
    $.get('../controller/AlmacenController.php?action=loadCampos_NuevoAlmacen_Ajax',{'idus':idustk, 'idserv':IdServicio}, function (response) {
        divResponse.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
        divTabla.hide();
        divResponse.show();
        sga.plugins.select2_search('.selectedClassSearch');
        sga.plugins.owlCarousel('.feedback-slider');
        $('#txtEmail').email_multiple();
    });
});

$(document).on('change','#depa_itm',function(){
    "use strict";
    let idPadre = $(this).val();
    let $selectID = $('#prov_itm');
    $selectID.empty();
    $selectID.prepend("<option></option>");
    sga.wait.append('#prov_itm');
    $.get('../controller/UbigeoController.php?action=lista_Hijos_Ubigeo_xIdPadre_JSON', {'id':idPadre},function (lista) {
        if(lista!== null && parseInt(lista.length)>0){
            for(let j=0; j<parseInt(lista.length); j++){
                let option = $("<option></option>");
                option.val(lista[j].id);
                option.text(lista[j].texto);
                $selectID.append(option);
            }
        }
    },"json").always(function () {
        sga.wait.remove('#prov_itm');
        sga.plugins.select2_search('#prov_itm');
    });
});

$(document).on('change','#prov_itm',function(){
    "use strict";
    let idPadre = $(this).val();
    let $selectID = $('#dist_itm');
    $selectID.empty();
    $selectID.prepend("<option></option>");
    sga.wait.append('#dist_itm');
    $.get('../controller/UbigeoController.php?action=lista_Hijos_Ubigeo_xIdPadre_JSON', {'id':idPadre},function (lista) {
        if(lista!== null && parseInt(lista.length)>0){
            for(let j=0; j<parseInt(lista.length); j++){
                let option = $("<option></option>");
                option.val(lista[j].id);
                option.text(lista[j].texto);
                $selectID.append(option);
            }
        }
    },"json").always(function () {
        sga.wait.remove('#dist_itm');
        sga.plugins.select2_search('#dist_itm');
    });
});

$(document).on('click', '#btnCancel', function(){
    "use strict";
    resetIni_Campos();
});

$(document).on('submit','#formNewAlmacen', function(e){
    "use strict";
    e.preventDefault();
    let valVale = $('#idowlcarousel').val();
    let data = $(this).serialize();
    if(parseInt(valVale) !== 0) {
        sga.blockUI.loading_body();
        $.post('../controller/AlmacenController.php?action=registrar_Almacen_JSON', data,
            function (response) {
                if (parseInt(response.status) === 1) {
                    swal({
                        text: "Registro realizado satisfactoriamente.",
                        type: "success",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Aceptar'
                    }).then(function () {
                        resetIni_Campos();
                    });
                } else if (parseInt(response.status) === 0) {
                    sga.error.show('danger', 'mensajes_actions_au', 'Error al realizar el registro');
                    window.setTimeout(function () {
                        $('#mensajes_actions_au').html("");
                    }, 6000);
                }
            }, "json").fail(function (e) {
            sga.error.show('danger', 'mensajes_actions_au', 'Error al realizar el registro');
            window.setTimeout(function () {
                $('#mensajes_actions_au').html("");
            }, 6000);
        }).always(function () {
            sga.blockUI.unblock_body();
        });
    }
    else{
        swal({
            text: "Debe seleccionar el vale a asignar al almacén.",
            type: "warning",
            showCancelButton: false,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            confirmButtonText: 'Aceptar'
        });
    }
});

$(document).on('click','#editarItem_Btn',function(){
    "use strict";
    let rows_selected = $Tbl_Almacen.column(1).checkboxes.selected();
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
        $.get('../controller/AlmacenController.php?action=loadCampos_EditarAlmacen_Ajax', {'id':arrayChk[0]}, function (response) {
            $divResponse.html(response);
        }).always(function () {
            sga.blockUI.unblock_body();
            $divTabla.hide();
            $divResponse.show();
            sga.plugins.select2_search('.selectedClassSearch');
            sga.plugins.owlCarousel('.feedback-slider');
            $('#txtEmail').email_multiple_edit();
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

$(document).on('click','#Eliminar_ValorLista', function() {
    "use strict";
    let $element = $(this).attr('data-id');
    $('#' + $element).remove();
});

$(document).on('submit','#formEditAlmacen', function(e) {
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
            $.post('../controller/AlmacenController.php?action=update_Almacen_JSON', $data, function(response){
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
                    sga.error.show('danger','mensajes_actions_au','Error al realizar la actualización del registro');
                    window.setTimeout(function () { $('#mensajes_actions_au').html("");}, 6000);
                }
            },"json").fail(function (e) {
                sga.error.show('danger','mensajes_actions_au',"Error al realizar la actualización de los datos, contactese con el Administrador del sistema, para generar un reporte del incidente.");
                window.setTimeout(function () { $('#mensajes_actions_au').html("");}, 6000);
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click','#fslider-item',function(){
    "use strict";
    let valeVal = $('#idowlcarousel');
    let valor = $(this).val();
    valeVal.val(valor);
});

$(document).on('click','#chkSemaforo',function(){
    "use strict";
    let inputDisable = $('.inputDisable');
    let inputEmail = $('#txtEmail');
    let appendMail = $('.all-mail');
    let emailTemp = $('#emailTemp');
    emailTemp.prop('disabled',true);
    inputDisable.prop('disabled',true);
    inputDisable.val("");
    emailTemp.val("");
    inputEmail.val("");
    appendMail.empty();
    let semambar = $('#semambar1');
    semambar.val("")
    let semred = $('#semred1');
    semred.val("")
    if( $(this).is(':checked') ) {
        inputDisable.prop('disabled',false);
        emailTemp.prop('disabled',false);
    }
});

$(document).on('keyup','#semverde2',function(){
    "use strict";
    let inputVerde = $(this);
    let inputAmbar = $('#semambar1');
    if($.trim(inputVerde.val()).length > 0 ){
        inputAmbar.val(parseInt($(this).val())+1);
    }
    else{
        inputAmbar.val("");
    }
});

$(document).on('keyup','#semambar2',function(){
    "use strict";
    let inputAmbar = $(this);
    let inputRed = $('#semred1');
    if($.trim(inputAmbar.val()).length > 0 ){
        inputRed.val(parseInt($(this).val())+1);
    }
    else{
        inputRed.val("");
    }
});

$(document).on('click','#btnDelEmail',function(){
    "use strict";
    let ixdexE = $(this).attr('data-index');
    let contend = $('#row-idmedia-'+ixdexE);
    contend.remove();
});