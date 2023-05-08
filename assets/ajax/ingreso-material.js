var Tbl_Material, idSignature;

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
    let countServicio = $('#count_servicio').val();
    if(parseInt(countServicio)===1){
        let IdServicioUsuario = $('#IdServicioUsuario').val();
        load_CamposIngresos(IdServicioUsuario);
    }
    else {
        sga.plugins.select2_search('.selectSearch');
    }
});

$(document).on('change', '#IdServicioUsuario', function() {
    "use strict";
    let IdServicioUsuario = $(this).val();
    load_CamposIngresos(IdServicioUsuario);
});

function load_CamposIngresos(id) {
    "use strict";
    let divCampoIngreso= $('#divIContend');
    divCampoIngreso.empty();
    sga.blockUI.loading_body();
    $.get('../controller/MaterialController.php?action=loadCampos_ingresoMaterial', {'idsu': id}, function (response) {
        divCampoIngreso.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
        let numberAlmacen = $('#count_almacen').val();
        let idAlmacen = 0;
        if(parseInt(numberAlmacen) === 1) {
            idAlmacen = $('#IdAlmacen').val();
        }
        else{
            sga.plugins.select2_search('.selectClass');
        }
        load_Ini_Tbl_Material_xAlmacen(idAlmacen);
        load_Ini_Tbl_Personal_xServicio(id);
    });
}

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

function load_Ini_Tbl_Material_xAlmacen(idAlmacen){
    "use strict";
    sga.blockUI.loading_body();
    Tbl_Material = $('#Tbl_Material').DataTable({
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
                className: 'control align-middle',
                orderable: false,
                searchable: false,
                targets: [0]
            },
            {
                'checkboxes': {
                    'selectRow': false
                },
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                width: "30px",
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
                targets: [6]
            },
            {
                className: 'text-center align-middle',
                targets: [7]
            },
            {
                className: 'text-center align-middle',
                targets: [8]
            },
            {
                className: 'text-center align-middle',
                orderable: false,
                searchable: false,
                targets: [9]
            }
        ],
        order:[2 , 'asc'],
        ajax:{
            url: '../controller/MaterialController.php?action=lista_Material_xAlmacen_JSON',
            type : "get",
            data : {'idAlmacen':idAlmacen},
            dataType : "json",
            error: function(e){
                console.log(e.responseText);
            }
        },
        "initComplete": function(settings, json) {
            if(idAlmacen != null) {
                let dtBbuttons = $('#Tbl_Material_wrapper > div.datatable-header > div.dt-buttons');
                let btnAddMaterial = '<button type="button" class="btn btn-warning float-right btn-sm mr-10" data-id="' + idAlmacen + '" id="btnAddMaterial" style="padding: 0.42rem 1.2rem;border-radius: 3px;" title="Importar"><i class="icon-cloud-upload fz-18 position-left"></i> Importar </button>';
                dtBbuttons.prepend(btnAddMaterial);
                if(parseInt(json.data.length) > 0) {
                    let btnCodBarraAll = '<button type="button" class="btn btn-danger btn-sm mr-10" id="btnGenPDFAll" style="padding: 0.42rem 1.2rem;border-radius: 3px;" title="Generar">' +
                        '<i class="fa fa-barcode fz-18 position-left"></i> Generar ' +
                        '</button>';
                    dtBbuttons.prepend(btnCodBarraAll);
                }
                dtBbuttons.addClass('p-r-20');
            }
            $('#Tbl_Material_wrapper > div.datatable-header > div.dataTables_filter > label > input').attr('style','margin-left: 15px !important');
            $('#Tbl_Material_wrapper > div.datatable-header > div.dataTables_length > label > select').addClass('mr-15');
            sga.blockUI.unblock_body();
        },
        "drawCallback": function( settings ) {
        }
    });
}

$(document).on('click','#btnGenPDFAll', function() {
    "use strict";
    let estado = $(this).attr('data-estd');
    let rows_selected = Tbl_Material.column(1).checkboxes.selected();
    let arrayChk = [];
    $.each(rows_selected, function(index, rowId){
        arrayChk.push(rowId);
    });
    if(arrayChk.length>0) {
        let datos = {
            'datos':arrayChk
        };
        generarPDF_CodeBar(arrayChk,2);
    }
    else{
        sga.plugins.toastr('error', 'toast-top-right', 'Debe seleccionar al menos un Registro', 'Error');
    }
});

$(document).on('change','#IdAlmacen', function() {
    "use strict";
    let IdAlmacen = $(this).val();
    Tbl_Material.clear().draw();
    Tbl_Material.destroy();
    load_Ini_Tbl_Material_xAlmacen(IdAlmacen);
});

$(document).on('click','#bajaAltaMat', function() {
    "use strict";
    let id = $(this).attr('data-id');
    let estado = $(this).attr('data-opc');
    sga.blockUI.loading_body();
    $.post('../controller/MaterialController.php?action=altaBaja_Material_JSON', {'id':id,'estado':estado}, function (response) {
        if (parseInt(response.status) === 1) {
            sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
            sga.table.refreshDatatable('#Tbl_Material');
        }
        else if (parseInt(response.status) === 0) {
            sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
        }
    },"json").fail(function (e) {
        sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar actualizar el estado del Material, vuelva a intentarlo y si el problema persiste contáctese con el Administrador', 'Error');
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#deleteMat', function() {
    "use strict";
    let id = $(this).attr('data-id');
    let opc = $(this).attr('data-opc');
    swal.fire({
        html: 'Se va a eliminar el Material seleccionado.<br>Una vez realizada esta acción no podrá ser revertida.<br>Desea continuar..!!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.value) {
            sga.blockUI.loading_body();
            $.post('../controller/MaterialController.php?action=delete_Material_JSON', {'id':id,'opc':opc}, function (response) {
                if (parseInt(response.status) === 1) {
                    sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
                    sga.table.refreshDatatable('#Tbl_Material');
                }
                else if (parseInt(response.status) === 0) {
                    sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
                }
            },"json").fail(function (e) {
                sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar Eliminar el Material seleccionado, vuelva a intentarlo y si el problema persiste contáctese con el Administrador.', 'Error');
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click','#editMat',function(){
    "use strict";
    let thisIdMaterial = $(this).attr('data-id');
    let thisIdAlmacen = $(this).attr('data-idalm');
    let divTabla = $('#divMTabla');
    let divResponse = $('#divMResponse');
    divTabla.show();
    divResponse.hide();
    sga.blockUI.loading_body();
    $.get('../controller/MaterialController.php?action=loadCampos_EditarMaterial', {'idmat':thisIdMaterial,'idalm':thisIdAlmacen}, function (response) {
        divResponse.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
        divTabla.hide();
        divResponse.show();
        sga.funcion.pageTop();
        sga.plugins.select2_inClear('.classSelect')
    });
});

$(document).on('click','#btnChangeCodMate',function(){
    "use strict";
    $(this).prop('disabled',true);
    let thisIdAlmacen = $(this).attr('data-idalm');
    let thisIdMat = $(this).attr('data-idmat');
    let thisCodeMaterial = $(this).attr('data-codmat');
    let divChange = $('#divchangeCodigo');
    divChange.empty();
    sga.wait.append('#divchangeCodigo');
    $.get('../controller/MaterialController.php?action=loadCampos_newCodMaterial', {'idalm':thisIdAlmacen,'idmat':thisIdMat,'codmat':thisCodeMaterial}, function (response) {
        divChange.append(response);
    }).always(function () {
        sga.wait.remove('#divchangeCodigo');
    });
});

$(document).on('click','#btnValidate_Cancel',function(){
    "use strict";
    $('#btnChangeCodMate').prop('disabled',false);
    let divChange = $('#divchangeCodigo');
    divChange.empty();
});

$(document).on('submit','#formValidate_Codigo', function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
    let divchangeCodigo = $('#divchangeCodigo');
    sga.blockUI.loading_body();
    $.post('../controller/MaterialController.php?action=valida_codmaterialNew_Item_JSON', data, function (response) {
        divchangeCodigo.empty();
        divchangeCodigo.append(response);
    }).always(function () {
        sga.blockUI.unblock_body();
    });
});

$(document).on('click','#btnActualizar_Cod',function(){
    "use strict";
    let cod = $(this).attr('data-cod');
    $('#btnChangeCodMate').prop('disabled',false);
    let divChange = $('#divchangeCodigo');
    let codInput = $('#codigo_i');
    let codText = $('#cod_temp');
    codInput.val(cod);
    codText.val(cod);
    divChange.empty();
});

$(document).on('submit','#formEditMaterial', function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
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
            $.post('../controller/MaterialController.php?action=actualizar_Material_JSON', data, function(response){
                if (parseInt(response.status) === 1) {
                    sga.plugins.toastr('success', 'toast-top-right', response.message, 'Success');
                    resetIni_CamposMaterial();
                }
                else if (parseInt(response.status) === 0) {
                    sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
                }
            },"json").fail(function (e) {
                sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar modificar el Material seleccionado, vuelva a intentarlo y si el problema persiste contáctese con el Administrador.', 'Error');
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});

$(document).on('click', '#btnCancel', function(){
    "use strict";
    resetIni_CamposMaterial();
});
/*
$(document).on('submit','#formEditMaterial', function(e) {
    "use strict";
    e.preventDefault();
    let data = $(this).serialize();
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
            $.post('../controller/MaterialController.php?action=actualizar_Material_JSON', data, function(response){
                if (parseInt(response.status) === 1) {
                    swal({
                        text: response.message,
                        type: "success",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Aceptar'
                    }).then(function () {
                        resetIni_CamposMaterial();
                    });
                }
                else if (parseInt(response.status) === 0) {
                    sga.plugins.toastr('error', 'toast-top-right', response.message, 'Error');
                }
            },"json").fail(function (e) {
                sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar actualizar el Material seleccionado, vuelva a intentarlo y si el problema persiste contáctese con el Administrador.', 'Error');
            }).always(function () {
                sga.blockUI.unblock_body();
            });
        }
    });
});*/

$(document).on('click','#btnAddMaterial',function(){
    "use strict";
    let thisIdAlmacen = $(this).attr('data-id');
    let divTabla = $('#divMTabla');
    let divResponse = $('#divMResponse');
    divTabla.show();
    divResponse.hide();
    sga.blockUI.loading_body();
    $.get('../controller/MaterialController.php?action=loadCampos_loadMaterial', {'idalm':thisIdAlmacen}, function (response) {
        divResponse.html(response);
    }).always(function () {
        sga.blockUI.unblock_body();
        divTabla.hide();
        divResponse.show();
        sga.plugins.inputFile('#file_data','excel');
        sga.funcion.pageTop();
    });
});

$(document).on('click', '#btnCancel_Load', function(){
    "use strict";
    resetIni_CamposMaterial();
});

$(document).on('submit','#fmrLoad_DataMaterial',function(e){
    "use strict";
    e.preventDefault();
    let btnDisabled = $('#btnDisabled');
    let mensajeIFile = $('#divMsg_iFile');
    btnDisabled.prop('disabled',true);
    mensajeIFile.empty();

    let idAlmacen = $('#IdAlmacen').val();
    let formdata = new FormData($(this)[0]);
    formdata.append('idalm',idAlmacen);
    $.ajax({
        url: '../controller/MaterialController.php?action=load_File_Material',
        type: "POST",
        data: formdata,
        dataType: "json",
        cache: false,
        contentType: false, //No especificamos ningún tipo de dato
        enctype: 'multipart/form-data',
        processData:false, //Evitamos que JQuery procese los datos, daría error
        beforeSend: function(){
            sga.plugins.inputFile_disable('#file_data');
            sga.wait.append('#divMsg_iFile');
        },
        success: function (response) {
            console.log(response);
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
                    sga.plugins.inputFile_clear('#file_data');
                    sga.plugins.inputFile_enable('#file_data');
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
                    resetIni_CamposMaterial();
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
                    resetIni_CamposMaterial();
                });
            }
        }
    }).fail(function (e) {
        sga.plugins.toastr('error', 'toast-top-right', 'Se produjo un error al intentar cargar la plantilla, vuelva a intentarlo y si el problema persiste contáctese con el Administrador.', 'Error');
        sga.plugins.inputFile_clear('#file_data');
        sga.plugins.inputFile_enable('#file_data');
        btnDisabled.prop('disabled',false);
    }).always(function () {
        sga.wait.remove('#divMsg_iFile');
    });
});

$(document).on('click','#btn_generatedCBar',function(){
    "use strict";
    let data = {
        'codigo' : $(this).attr('data-cod'),
        'descrip' : $(this).attr('data-des'),
        'unidadM' : $(this).attr('data-um')
    };
    let modalLoading = $('#ModalProgressBar_Load');
    sga.wait.modal('ModalProgressBar_Load');
    modalLoading.modal("show");
    let modalDefault = $('#ModalAction_ContainerForm');
    $.get('../controller/MaterialController.php?action=generatedCodeBar_Material', data,function (response) {
        modalDefault.html(response);
    }).always(function () {
        modalLoading.modal("hide");
        modalLoading.empty();
        modalLoading.hide();
        modalDefault.modal("show");
    });
});

$(document).on('click','#printCodebar',function(){
    "use strict";
    let openWindow = window.open("", "", "_blank");
    openWindow.document.write($('#displayCB').parent().html());
    openWindow.document.write(
        '<style>'+
            'b#title{font-size: 38px;}'+
            'div#codeCB {' +
            '   font-weight: 700;\n' +
            '   font-size: 64px;\n' +
            '   text-align: justify;\n' +
            '   text-align-last: justify;\n' +
            '   margin: 0 40px 0 40px;\n' +
            '}'+
            '#fieldCB img{' +
            '   height: 42vh;' +
            '   max-width: 100%;' +
            '}'+
        '</style>'
    );
    openWindow.document.close();
    openWindow.focus();
    openWindow.print();
    // openWindow.close();
    setTimeout(function(){
        openWindow.close();
    },1000)
})

$(document).on('click','#downloadCodebar',function(){
    "use strict";
    let codigo = $(this).attr("data-code");
    html2canvas($('#fieldCB'), {
        onrendered: function(canvas) {
            let img = canvas.toDataURL("image/png");
            let uri = img.replace(/^data:image\/[^;]/, 'data:application/octet-stream');
            let link = document.createElement('a');
            if (typeof link.download === 'string') {
                document.body.appendChild(link);
                link.download = 'barcode_'+codigo+'.png';
                link.href = uri;
                link.click();
                document.body.removeChild(link);
            } else {
                location.replace(uri);
            }

        }
    });
})

$(document).on('click','#pdfCreatorCodebar',function () {
    "use strict";
    let data = {
        'cod' : $(this).attr('data-code'),
        'des' : $(this).attr('data-des'),
        'um' : $(this).attr('data-um')
    };
    generarPDF_CodeBar(data,1);
});

function generarPDF_CodeBar(data,option) {
    "use strict";
    let page;
    if(parseInt(option) === 1) {
        page = '../app/Export-CodeBarra-Material-PDF.php?option=' + option + '&cod=' + data.cod + '&des=' + data.des + '&um=' + data.um;
    }
    else if(parseInt(option) === 2) {
        page = '../app/Export-CodeBarra-Material-PDF.php?option=' + option + '&datos=' + data;
    }
    $.ajax({
        url: page,
        type: 'POST',
        beforeSend: function() {
            sga.blockUI.loading_body();
        },
        success: function(){
            window.location = page;// you can use window.open also
            sga.table.refreshDatatable('#Tbl_Material');
            sga.blockUI.unblock_body();
        },
        error: function(xhr) { // if error occured
            console.log("Error occured.please try again");
            console.log(xhr.statusText + " - " +xhr.responseText);
        }
    });
}

function resetIni_CamposMaterial() {
    "use strict";
    let divTabla = $('#divMTabla');
    let divResponse = $('#divMResponse');
    divTabla.show();
    divResponse.hide();
    divResponse.empty();
    sga.table.refreshDatatable('#Tbl_Material');
    sga.funcion.pageTop();
}