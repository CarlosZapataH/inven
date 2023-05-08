var swal, Tbl_Calibracion, tblcali;

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

tblcali =
    '<div class="card card-shadow mb-20">' +
    '   <div class="card-header">' +
    '       <div class="card-title" id="divSem"></div>' +
    '   </div>' +
    '   <div class="text-center" id="mensaje_actions"></div>'+
    '   <table class="table table-striped table-hover" id="TblCalibracion">' +
    '       <thead>' +
    '           <tr>' +
    '               <th scope="col" class="text-center align-middle">#</th>' +
    '               <th scope="col" class="text-center align-middle">Codigo</th>' +
    '               <th scope="col" class="text-center align-middle">Descripción</th>' +
    '               <th scope="col" class="text-center align-middle">Número<br>Parte/Serie</th>' +
    '               <th scope="col" class="text-center align-middle">F. Ult.<br>Calibrac.</th>' +
    '               <th scope="col" class="text-center align-middle">Frec.<br>Calibrac.</th>' +
    '               <th scope="col" class="text-center align-middle">Proxima<br>Calibrac.</th>' +
    '               <th scope="col" class="text-center align-middle">Días vencim.</th>' +
    '               <th scope="col" class="text-center align-middle">Ult.Estado.</th>' +
    '               <th scope="col" class="text-center align-middle"></th>' +
    '               <th scope="col" class="text-center align-middle"></th>' +
    '               <th scope="col" class="text-center align-middle"></th>' +
    '           </tr>' +
    '       </thead>' +
    '       <tbody></tbody>'+
    '   </table>'+
    '</div>';

$(function() {
    "use strict";
    sga.plugins.flatpickr_rangeInput('#fecha_ajt');
    sga.plugins.formatter_daterange('#fecha_ajt');
    //sga.plugins.select2('#IdAlmacen');

    let countServicio = $('#count_servicio').val();
    if(parseInt(countServicio)===1){
        let countAlmacen = $('#count_almacen').val();
        if(parseInt(countAlmacen)===1){
            let IdAlmacen = $('#IdAlmacen').val();
            loadTbl_Calibracion_xAlmacen(IdAlmacen,null);
        }
    }
    else {
        sga.plugins.select2_search('.selectClass');
    }
});

$(document).on('change', '#IdServicioUsuario', function() {
    "use strict";
    let IdSerUsuario = $(this).val();
    load_Almacenes_xServicioUsuario(IdSerUsuario);
});

function load_Almacenes_xServicioUsuario(id) {
    "use strict";
    let ubic = $('#ubic_ajt');
    ubic.empty();
    if(id !== "") {
        let $selectID = $('#IdAlmacen');
        $selectID.empty();
        $selectID.prop('disabled', true);
        $.get('../controller/AlmacenController.php?action=loadSelect_Almacen_ServicioUsuario_JSON', {'idsu': id}, function (lista) {
            $selectID.empty();
            $selectID.append('<option></option>');
            if (lista !== null && parseInt(lista.length) > 0) {
                for (let i = 0; i < parseInt(lista.length); i++) {
                    let $option = $("<option></option>");
                    $option.val(lista[i].id);
                    $option.text(lista[i].texto);
                    $option.attr('data-vista',lista[i].vista);
                    $selectID.append($option);
                }
            }
            ubic.append('<option value="">Seleccione...</option>');
        }, "json").always(function () {
            $selectID.prop('disabled', false);
            sga.plugins.select2('.selectClass');
        });
    }
}

$(document).on('click', '.btnRdbSemaforo', function() {
    "use strict";
    let TipoSemaforo = $(this).attr('data-val');
    let IdAlmacen = $('#IdAlmacen').val();
    loadTbl_Calibracion_xAlmacen(IdAlmacen,TipoSemaforo);
});

$(document).on('change', '#IdAlmacen', function() {
    "use strict";
    let IdAlmacen = $(this).val();
    loadTbl_Calibracion_xAlmacen(IdAlmacen,null);
});

function loadTbl_Calibracion_xAlmacen(IdAlmacen,colorSemafore) {
    "use strict";
    sga.blockUI.loading_body();
    let elementSelect = $('#IdAlmacen option:selected');
    let datos = {
        'IdAlmacen':IdAlmacen,
        'nameAlmacen':elementSelect.text(),
        'idustk': $('#idustk').val(),
        'colorSemaforo':colorSemafore
    }
    let divTabla = $('#divTabla');
    let divResponse = $('#divResponse');
    divTabla.empty();
    divTabla.append(tblcali);
    divResponse.empty();

    Tbl_Calibracion = $('#TblCalibracion').DataTable({
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm mr-15',
                title: 'LISTA DE CALIBRACIÓN : '+elementSelect.text(),
                text: '<span class="fa fa-file-excel-o position-left"></span> Exportar',
                filename: function(){
                    let f = new Date();
                    return 'Export-List-calibration-' + f.getDate() + (f.getMonth() +1) + f.getFullYear();
                },
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied'
                    }
                }
            }
        ],
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
            { "mDataProp": "7"},
            { "mDataProp": "8"},
            { "mDataProp": "9"},
            { "mDataProp": "10"},
            { "mDataProp": "11"},
            { "mDataProp": "12"}
        ],
        columnDefs: [
            {
                className: 'text-center',
                targets: [0],
                orderable: false,
                searchable: false
            },
            {
                className: 'text-center',
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
                className: 'text-center',//d-none
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
                orderable: false,
                className: 'text-center',
                targets: [10],
                width: "120px",
            },
            {
                className: 'text-center',
                visible: false,
                searchable: false,
                targets: [11]
            },
        ],
        lengthMenu: [ [7, 10, 25, 50, -1], [7, 10, 25, 50, "Todos"] ],
        ajax:{
            url: '../controller/InventarioController.php?action=lst_Calibracion_xIdAlmacen_JSON',
            type : "get",
            data : datos,
            dataType : "json",
            error: function(e){
                sga.error.show('danger', 'mensaje_actions', e.responseText);
                window.setTimeout(function () {$('#mensaje_actions').html("");}, 6000);
            }
        },
        "initComplete": function(settings, json) {
            sga.blockUI.unblock_body();
            $('#TblCalibracion_filter > label > input').addClass('ml-10');
            $('#TblCalibracion_length > label > select').addClass('mr-10');
            console.log(json.data.length);
            let divSemaforo = $('#divSem');
            divSemaforo.empty();
            /*let dtBbuttons = $('#TblCalibracion_wrapper > div.datatable-header > div.dt-buttons');*/
            let semaforoBtn = 'Estado de calibración:';
            semaforoBtn +='<div class="btn-group float-right" data-toggle="buttons">';
            if(colorSemafore === "r") {
                semaforoBtn +='<label class="btn btn-outline-danger btn-sm btnRdbSemaforo" data-val="r">';
                semaforoBtn +=' <input type="radio" name="optSemaforo" class="" value="r" checked> Rojo';
                semaforoBtn +='</label>';
            }
            else{
                semaforoBtn +='<label class="btn btn-outline-danger btn-sm btnRdbSemaforo" data-val="r">';
                semaforoBtn +=' <input type="radio" name="optSemaforo" class="" value="r"> Rojo';
                semaforoBtn +='</label>';
            }

            if(colorSemafore === "a") {
                semaforoBtn +='<label class="btn btn-outline-warning btn-sm btnRdbSemaforo" data-val="a">';
                semaforoBtn +=' <input type="radio" name="optSemaforo" class="" value="a" checked> Ambar';
                semaforoBtn +='</label>';
            }
            else{
                semaforoBtn +='<label class="btn btn-outline-warning btn-sm btnRdbSemaforo" data-val="a">';
                semaforoBtn +=' <input type="radio" name="optSemaforo" class="" value="a"> Ambar';
                semaforoBtn +='</label>';
            }

            if(colorSemafore === "v") {
                semaforoBtn +='<label class="btn btn-outline-success btn-sm btnRdbSemaforo" data-val="v">';
                semaforoBtn +=' <input type="radio" name="optSemaforo" class="" value="v" checked> Verde';
                semaforoBtn +='</label>';
            }
            else{
                semaforoBtn +='<label class="btn btn-outline-success btn-sm btnRdbSemaforo" data-val="v">';
                semaforoBtn +=' <input type="radio" name="optSemaforo" class="" value="v"> Verde';
                semaforoBtn +='</label>';
            }
            divSemaforo.append(semaforoBtn);

            if(json.data.length > 0){

            }
        },
        "drawCallback": function( settings ) {
            sga.blockUI.unblock_body();
        }
    });
}

$(document).on('click', '#updateCalibra', function() {
    "use strict";
    let IdInventario = $(this).attr('data-idinv');
    let IdAlmacen = $(this).attr('data-idalm');
    let fechaultCalibra = $(this).attr('data-fuc');
    let divHead = $('#divHead');
    let divTabla = $('#divTabla');
    let divResponse = $('#divResponse');
    let datos = {
        'IdInventario':IdInventario,
        'IdAlmacen':IdAlmacen,
        'idustk': $('#idustk').val()
    }
    divHead.show();
    divTabla.show();
    divResponse.empty();
    sga.blockUI.loading_body();
    $.get('../controller/InventarioController.php?action=loadCampos_renew_calibracion', datos, function (response) {
        divResponse.append(response);
    }).always(function () {
        divHead.hide();
        divTabla.hide();
        sga.blockUI.unblock_body();
        sga.plugins.flatpickr_mindate('.inputFecha',fechaultCalibra);
        sga.plugins.formatter_date('.inputFecha', '/');
        sga.plugins.inputFile('#filedata_cal','pdf');
    });
});

$(document).on('click', '#btnCancel', function(){
    "use strict";
    reset_campos();
});

function reset_campos() {
    let divHead = $('#divHead');
    let divTabla = $('#divTabla');
    let divResponse = $('#divResponse');
    divHead.show();
    divTabla.show();
    divResponse.empty();
    sga.table.refreshDatatable_chk('#TblCalibracion');
}

$(document).on('submit','#formCalibrationItem',function(e){
    "use strict";
    e.preventDefault();
    let fechaCalibraID = $('#fecha_cal');
    if(sga.funcion.existeFecha(fechaCalibraID.val()) === true) {
        let idInventario = $('#idinv_c').val();
        let idAlmacen = $('#idalm_c').val();
        let idUsuario = $('#idustk_c').val();
        let formdata = new FormData($(this)[0]);
        formdata.append('idInventario', idInventario);
        formdata.append('idAlmacen', idAlmacen);
        formdata.append('idUsuario', idUsuario);
        $.ajax({
            url: '../controller/InventarioController.php?action=register_date_Calibracion_JSON',
            type: "POST",
            data: formdata,
            dataType: "json",
            cache: false,
            contentType: false, //No especificamos ningún tipo de dato
            enctype: 'multipart/form-data',
            processData: false, //Evitamos que JQuery procese los datos, daría error
            beforeSend: function () {
                sga.blockUI.loading_body();
            },
            success: function (response) {
                if (parseInt(response.status) === 0) {
                    swal.fire({
                        text: response.message,
                        type: "error",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Aceptar'
                    }).then(function () {
                        reset_campos();
                    });
                } else if (parseInt(response.status) === 1) {
                    swal.fire({
                        text: response.message,
                        type: "success",
                        showCancelButton: false,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Aceptar'
                    }).then(function () {
                        reset_campos();
                    });
                }
            }
        }).fail(function () {
            sga.error.show('danger', 'mensajes_actions', "Error al intentar realizar el registro de la nueva fecha de calibracion del Activo/Instrumento.");
            window.setTimeout(function () { $('#mensajes_actions').html(""); }, 6000);
        }).always(function () {
            sga.blockUI.unblock_body();
        });
    }
    else{
        sga.error.show('danger', 'mensajes_actions', "Debe ingresar una fecha de calibración valida");
        window.setTimeout(function () { $('#mensajes_actions').html(""); }, 6000);
        fechaCalibraID.val("");
        fechaCalibraID.focus();
    }
});

$(document).on('click', '#historyCalibra', function() {
    "use strict";
    let IdInventario = $(this).attr('data-idinv');
    let IdAlmacen = $(this).attr('data-idalm');
    let divHead = $('#divHead');
    let divTabla = $('#divTabla');
    let divResponse = $('#divResponse');
    let datos = {
        'IdInventario':IdInventario,
        'IdAlmacen':IdAlmacen,
    }
    divHead.show();
    divTabla.show();
    divResponse.empty();
    sga.wait.append('#divResponse');
    $.get('../controller/InventarioController.php?action=load_Historial_Calibracion_xIdInventario', datos, function (response) {
        divResponse.append(response);
    }).always(function () {
        divHead.hide();
        divTabla.hide();
        sga.wait.remove('#divResponse');
    });
});

$(document).on('click','#overviewCalibrate',function(){
    "use strict";
    let fileName = $(this).attr('data-file');
    let filedes = $(this).attr('data-des');
    let modalLoading = $('#ModalProgressBar_Load');
    sga.wait.modal('ModalProgressBar_Load');
    modalLoading.modal("show");
    let modalDefault = $('#ModalAction_ContainerForm');
    let data = {'description':filedes};
    $.get('../controller/InventarioController.php?action=load_view_Pdf_Modal', data, function (response) {
        modalDefault.html(response);
        viewPDF_Object('#filePDFContend',fileName);
    }).always(function () {
        modalLoading.modal("hide");
        modalLoading.html("");
        modalLoading.hide();
        modalDefault.modal("show");
    });
});

function viewPDF_Object(container,filename) {
    "use strict";
    let options = {
        pdfOpenParams: {
            navpanes: 1,
            toolbar: 1,
            statusbar: 1
        }
    };
    PDFObject.embed("../assets/certificate-calibration/"+filename, container,options);
}

$(document).on('click','#btnCrearPdf',function () {
    "use strict";
    let codInventario = $(this).attr('data-cod');
    sga.blockUI.loading_body();
    const element = document.getElementById('divHtml2pdf');
    let opt = {
        margin:       0.25,
        filename:     'HISTORIAL-'+codInventario+'.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { dpi: 192, letterRendering: false, scale: 2  },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
    };

    const opt1 = {
        filename:'HISTORIAL-'+codInventario+'.pdf',
        margin: 0.15,
        image: {type: 'jpeg', quality: 0.9},
        jsPDF: {format: 'A4', orientation: 'landscape'},
        pagebreak: {mode: 'css' }
    };

    html2pdf()
        .set(opt1)
        .from(element)
        .save()
        .catch(err => console.log(err))
        .finally(() => {
            sga.blockUI.unblock_body();
        });
});