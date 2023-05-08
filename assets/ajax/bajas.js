var swal;

$(function() {
    "use strict";
    let countServicio = $('#count_servicio').val();
    if(parseInt(countServicio)===1){
        let countAlmacen = $('#count_almacen').val();
        if(parseInt(countAlmacen)===1){
            let IdAlmacen = $('#IdAlmacen').val();
            load_Inventario_Bajas_xAlmacen(IdAlmacen);
        }
    }
    else {
        sga.plugins.select2('.selectClass');
    }
});

$(document).on('change', '#IdServicioUsuario', function() {
    "use strict";
    let IdSerUsuario = $(this).val();
    load_Almacenes_xServicioUsuario(IdSerUsuario);
});

function load_Almacenes_xServicioUsuario(id) {
    "use strict";
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
    }, "json").always(function () {
        $selectID.prop('disabled', false);
        sga.plugins.select2('.selectClass');
    });
}

$(document).on('change', '#IdAlmacen', function() {
    "use strict";
    let IdAlmacen = $(this).val();
    load_Inventario_Bajas_xAlmacen(IdAlmacen);
});

function load_Inventario_Bajas_xAlmacen(IdAlmacen) {
    "use strict";
    let elementSelect = $('#IdAlmacen option:selected')
    let containerID = $('#divResponse');
    let datos = {
        'IdAlmacen':IdAlmacen,
        'nameAlmacen':elementSelect.text()
    }
    containerID.empty();
    sga.wait.append('#divResponse');
    $.get('../controller/InventarioController.php?action=lst_Inventario_Bajas_xIdAlmacen', datos, function (response) {
        containerID.append(response);
    }).always(function () {
        sga.wait.remove('#divResponse');
    });
}

$(document).on('click','#overviewBaja',function(){
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
    PDFObject.embed("../assets/certificate-baja/"+filename, container,options);
}